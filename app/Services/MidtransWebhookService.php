<?php

namespace App\Services;

use App\Models\User;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Jobs\ProcessSuccessfulOrderJob;
use App\Notifications\NewOrderNotification;
use Illuminate\Support\Facades\Notification;
use App\Jobs\ProcessSuccessfulSubscriptionJob;
use Midtrans\Notification as MidtransNotification;
use App\Notifications\PartnerActivatedNotification;
use App\Notifications\CustomerPaymentSuccessNotification;

class MidtransWebhookService
{
    /**
     * Menangani notifikasi webhook dari Midtrans.
     */
    public function handle(MidtransNotification $notification): void
    {
        Log::info('MidtransWebhookService: Handle dipanggil.', ['order_id_midtrans' => $notification->order_id]);

        $payment = Payment::where('midtrans_order_id', $notification->order_id)->first();

        if (!$payment) {
            Log::warning('MidtransWebhookService: Payment dengan midtrans_order_id tidak ditemukan.', ['midtrans_order_id' => $notification->order_id]);
            return;
        }

        if ($payment->midtrans_transaction_status === $notification->transaction_status) {
            Log::info('MidtransWebhookService: Status pembayaran sama dengan sebelumnya, diabaikan.', ['payment_id' => $payment->id, 'status' => $notification->transaction_status]);
            return;
        }

        switch ($notification->transaction_status) {
            case 'settlement':
            case 'capture':
                $this->processSuccessfulPayment($payment, $notification);
                break;
            case 'expire':
            case 'cancel':
            case 'deny':
                $this->processFailedPayment($payment, $notification);
                break;
            default:
                Log::info('MidtransWebhookService: Status transaksi lain diterima dan diabaikan.', ['status' => $notification->transaction_status]);
                break;
        }
    }

    /**
     * Memproses pembayaran yang GAGAL, KEDALUWARSA, atau DIBATALKAN.
     */
    protected function processFailedPayment(Payment $payment, MidtransNotification $notification)
    {
        Log::warning('Info Pembayaran Gagal/Expire Diterima', ['payment_id' => $payment->id, 'status' => $notification->transaction_status]);

        DB::transaction(function () use ($payment, $notification) {
            $payment->update([
                'midtrans_transaction_status' => $notification->transaction_status,
                'midtrans_response' => $notification->getResponse(),
            ]);

            $order = $payment->order;
            if ($order && $order->status !== 'completed') {
                $order->update(['status' => 'failed']);
                Log::info('MidtransWebhookService: Status order berhasil diupdate menjadi failed.', ['order_id' => $order->id]);
            }
        });
    }

    /**
     * Memproses pembayaran yang sukses dengan MEMPERBARUI data dan menjalankan semua logika secara langsung.
     */
    protected function processSuccessfulPayment(Payment $payment, MidtransNotification $notification)
    {
        try {
            DB::transaction(function () use ($payment, $notification) {
                // Langkah 1: Update status Payment
                $payment->update([
                    'midtrans_transaction_status' => $notification->transaction_status,
                    'midtrans_response' => $notification->getResponse(),
                ]);

                $order = $payment->order;
                if (!$order) {
                    Log::error('MidtransWebhookService: Relasi Order pada Payment tidak ditemukan.', ['payment_id' => $payment->id]);
                    return; // Hentikan jika order tidak ada
                }

                // Langkah 2: Update status Order menjadi 'completed'
                $order->update(['status' => 'completed']);
                Log::info('MidtransWebhookService: Status order dan payment berhasil diupdate.', ['order_id' => $order->id]);

                // Langkah 3: Jalankan logika finalisasi berdasarkan tipe pembayaran
                if ($payment->subs_package_id) {
                    // Ini adalah pembayaran langganan, jalankan aktivasi
                    $this->activateSubscription($order->user);
                } else {
                    // Ini adalah pembayaran produk, jalankan finalisasi pesanan
                    $this->finalizeProductOrder($order);
                }
            });

        } catch (\Exception $e) {
            Log::critical('MidtransWebhookService: GAGAL saat memproses transaksi database.', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Menyelesaikan pesanan produk dan mengirim notifikasi.
     * Logika ini diambil dari ProcessSuccessfulOrderJob.
     */
    protected function finalizeProductOrder(Order $order)
    {
        Log::info('FinalizeProductOrder: Memulai finalisasi pesanan.', ['order_id' => $order->id]);
        // Update status pengiriman jika ada
        if ($order->shipping) {
            $order->shipping->update(['status' => 'preparing_shipment']);
            Log::info('FinalizeProductOrder: Status shipping diupdate ke preparing_shipment.', ['order_id' => $order->id]);
        }

        try {
            $customer = $order->user;
            $shopOwner = $order->subdomain->user;

            // Kirim notifikasi ke pelanggan
            Notification::send($customer, new CustomerPaymentSuccessNotification($order));
            Log::info('Notifikasi sukses pembayaran dikirim ke pelanggan.', ['order_id' => $order->id, 'customer_id' => $customer->id]);

            // Kirim notifikasi ke mitra (pemilik toko)
            Notification::send($shopOwner, new NewOrderNotification($order));
            Log::info('Notifikasi pesanan baru dikirim ke mitra.', ['order_id' => $order->id, 'partner_id' => $shopOwner->id]);

        } catch (\Exception $e) {
            Log::error('FinalizeProductOrder: Gagal mengirim notifikasi.', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Mengaktifkan langganan dan mengubah role pengguna.
     * Logika ini diambil dari ProcessSuccessfulSubscriptionJob.
     */
    protected function activateSubscription(User $user)
    {
        Log::info('ActivateSubscription: Memulai aktivasi langganan.', ['user_id' => $user->id]);
        if (!$user || !$user->userPackage) {
            Log::error('ActivateSubscription: User atau UserPackage tidak valid.', ['user_id' => $user->id ?? null]);
            return;
        }

        if ($user->hasRole('mitra')) {
            Log::info('ActivateSubscription: Aktivasi dihentikan karena user sudah menjadi mitra.', ['user_id' => $user->id]);
            return;
        }

        $userPackage = $user->userPackage;
        $activeDate = Carbon::now();
        $expiredDate = $userPackage->plan_type === 'yearly' ? $activeDate->copy()->addYear() : $activeDate->copy()->addMonth();

        $userPackage->update(['status' => 'active', 'active_date' => $activeDate, 'expired_date' => $expiredDate]);
        
        $user->removeRole('calon-mitra');
        $user->assignRole('mitra');

        if ($user->subdomain) {
            $user->subdomain->update(['status' => 'active']);
        }

        try {
            Notification::send($user, new PartnerActivatedNotification($user));
            Log::info('ActivateSubscription: Aktivasi akun dan notifikasi berhasil.', ['user_id' => $user->id]);
        } catch (\Exception $e) {
            Log::error('ActivateSubscription: Gagal mengirim notifikasi aktivasi.', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
