<?php

namespace App\Services;

use App\Models\User;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Notifications\NewOrderNotification;
use Illuminate\Support\Facades\Notification;
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

        // Cari pembayaran berdasarkan Midtrans Order ID
        $payment = Payment::where('midtrans_order_id', $notification->order_id)->first();

        if (!$payment) {
            Log::warning('MidtransWebhookService: Payment dengan midtrans_order_id tidak ditemukan.', ['midtrans_order_id' => $notification->order_id]);
            return;
        }

        // Jangan proses notifikasi yang sama dua kali
        if ($payment->midtrans_transaction_status === $notification->transaction_status) {
            Log::info('MidtransWebhookService: Status pembayaran sama dengan sebelumnya, diabaikan.', ['payment_id' => $payment->id, 'status' => $notification->transaction_status]);
            return;
        }

        // Panggil method yang sesuai berdasarkan status transaksi
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
            // Langkah A: Perbarui catatan pembayaran
            $payment->update([
                'midtrans_transaction_status' => $notification->transaction_status,
                'midtrans_response' => $notification->getResponse(),
            ]);

            // Langkah B: Update Status Order terkait menjadi 'failed'
            $order = $payment->order;
            if ($order && $order->status !== 'completed') {
                $order->update(['status' => 'failed']);
                Log::info('MidtransWebhookService: Status order berhasil diupdate menjadi failed.', ['order_id' => $order->id]);
            }
        });
    }

    /**
     * Memproses pembayaran yang sukses dengan MEMPERBARUI data.
     */
    protected function processSuccessfulPayment(Payment $payment, MidtransNotification $notification)
    {
        Log::info('Info Pembayaran Sukses Diterima', ['payment_id' => $payment->id]);

        try {
            DB::transaction(function () use ($payment, $notification) {
                // Langkah A: Perbarui catatan pembayaran yang sudah ada
                $payment->update([
                    'midtrans_transaction_status' => $notification->transaction_status,
                    'midtrans_response' => $notification->getResponse(),
                ]);
                Log::info('MidtransWebhookService: Catatan pembayaran berhasil diperbarui.', ['payment_id' => $payment->id]);

                // Langkah B: Update Status Order terkait
                $order = $payment->order;
                if ($order) {
                    if ($payment->subs_package_id) {
                        $order->update(['status' => 'completed']);
                    } else {
                        $order->update(['status' => 'completed']);
                    }
                    Log::info('MidtransWebhookService: Status order berhasil diupdate menjadi completed.', ['order_id' => $order->id]);
                } else {
                    Log::error('MidtransWebhookService: Relasi Order pada Payment tidak ditemukan.', ['payment_id' => $payment->id]);
                }
            });
        } catch (\Exception $e) {
            Log::critical('MidtransWebhookService: GAGAL saat memproses transaksi database.', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);
            // Hentikan proses jika update database gagal
            return;
        }

        // Langkah C: Lakukan side-effect (seperti notifikasi) SETELAH transaksi DB berhasil.
        // Dengan cara ini, jika notifikasi gagal, status order tetap 'completed'.
        $order = $payment->order()->first(); // Ambil ulang data order yang sudah di-update
        if ($order && $order->status === 'completed') {
            if ($payment->subs_package_id) {
                $this->activateSubscription($order->user);
            } else {
                $this->finalizeProductOrder($order);
            }
        }
    }

    /**
     * Menyelesaikan pesanan produk dan mengirim notifikasi.
     */
    protected function finalizeProductOrder(Order $order)
    {
        // Update status pengiriman jika ada
        if ($order->shipping) {
            $order->shipping->update(['status' => 'preparing_shipment']);
        }

        // Bungkus pengiriman notifikasi dalam try-catch
        try {
            $customer = $order->user;
            $shopOwner = $order->subdomain->user;

            Notification::send($customer, new CustomerPaymentSuccessNotification($order));
            Log::info('Notifikasi sukses pembayaran dikirim ke pelanggan.', ['order_id' => $order->id, 'customer_id' => $customer->id]);

            Notification::send($shopOwner, new NewOrderNotification($order));
            Log::info('Notifikasi pesanan baru dikirim ke mitra.', ['order_id' => $order->id, 'partner_id' => $shopOwner->id]);

        } catch (\Exception $e) {
            // Jika notifikasi gagal, cukup catat error-nya tanpa mengganggu alur utama.
            Log::error('MidtransWebhookService: Gagal mengirim notifikasi pesanan.', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Mengaktifkan langganan dan mengubah role pengguna.
     */
    protected function activateSubscription(User $user)
    {
        if (!$user || !$user->userPackage) {
            Log::error('MidtransWebhookService: User atau UserPackage tidak valid untuk aktivasi.', ['user_id' => $user->id ?? null]);
            return;
        }

        if ($user->hasRole('mitra')) {
            Log::info('MidtransWebhookService: Aktivasi dihentikan karena user sudah menjadi mitra.', ['user_id' => $user->id]);
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

        Notification::send($user, new PartnerActivatedNotification($user));
        Log::info('MidtransWebhookService: Aktivasi akun berhasil.', ['user_id' => $user->id]);
    }
}
