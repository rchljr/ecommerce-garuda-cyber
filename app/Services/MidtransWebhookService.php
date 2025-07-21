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

        $payment = Payment::where('midtrans_order_id', $notification->order_id)->first();

        if (!$payment) {
            Log::warning('MidtransWebhookService: Payment dengan midtrans_order_id tidak ditemukan.', ['midtrans_order_id' => $notification->order_id]);
            return;
        }

        if ($payment->midtrans_transaction_status === 'settlement' || $payment->midtrans_transaction_status === 'capture') {
            Log::info('MidtransWebhookService: Pembayaran sudah berhasil diproses sebelumnya, diabaikan.', ['payment_id' => $payment->id, 'status' => $notification->transaction_status]);
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

            // Gunakan order_group_id dari payment
            if ($payment->order_group_id) {
                $orders = Order::where('order_group_id', $payment->order_group_id)->get();
                foreach ($orders as $order) {
                    if ($order->status !== 'completed') {
                        $order->update(['status' => 'failed']);
                        Log::info('MidtransWebhookService: Status order berhasil diupdate menjadi failed.', ['order_id' => $order->id]);
                    }
                }
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

                if ($payment->order_group_id) {
                    // Ini adalah pembayaran produk (single/multi-toko)
                    $this->finalizeGroupedProductOrder($payment);
                } elseif ($payment->subs_package_id && $payment->order) {
                    // Ini adalah pembayaran langganan
                    $order = $payment->order;
                    if ($order) {
                        $order->update(['status' => 'completed']);
                        $this->activateSubscription($order->user);
                    } else {
                        Log::error('MidtransWebhookService: Relasi Order pada Payment langganan tidak ditemukan.', ['payment_id' => $payment->id]);
                    }
                } else {
                    Log::error('MidtransWebhookService: Tipe pembayaran tidak dikenali.', ['payment_id' => $payment->id]);
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
     * Menyelesaikan semua pesanan produk dalam satu grup dan mengirim notifikasi.
     */
    protected function finalizeGroupedProductOrder(Payment $payment)
    {

        $orderGroupId = $payment->order_group_id;
        Log::info('FinalizeGroupedProductOrder: Memulai finalisasi pesanan untuk grup.', ['order_group_id' => $orderGroupId]);

        // Ambil semua order yang terkait dengan grup ini
        $orders = Order::where('order_group_id', $orderGroupId)->get();

        if ($orders->isEmpty()) {
            Log::error('FinalizeGroupedProductOrder: Tidak ada order yang ditemukan untuk grup.', ['order_group_id' => $orderGroupId]);
            return;
        }

        foreach ($orders as $order) {
            // Langkah 2: Update status setiap Order menjadi 'completed'
            $order->update(['status' => 'completed']);
            Log::info('FinalizeGroupedProductOrder: Status order diupdate.', ['order_id' => $order->id]);

            // Langkah 3: Finalisasi setiap pesanan (update shipping & kirim notif)
            $this->finalizeSingleProductOrder($order);
        }
    }

    /**
     * Menyelesaikan satu pesanan produk dan mengirim notifikasi.
     */
    protected function finalizeSingleProductOrder(Order $order)
    {
        Log::info('FinalizeSingleProductOrder: Memulai finalisasi untuk satu pesanan.', ['order_id' => $order->id]);

        if ($order->shipping) {
            $order->shipping->update(['status' => 'preparing_shipment']);
            Log::info('FinalizeSingleProductOrder: Status shipping diupdate.', ['order_id' => $order->id]);
        }

        try {
            $customer = $order->user;
            $shopOwner = $order->subdomain->user;

            if ($customer) {
                Notification::send($customer, new CustomerPaymentSuccessNotification($order));
                Log::info('Notifikasi sukses pembayaran dikirim ke pelanggan.', ['order_id' => $order->id, 'customer_id' => $customer->id]);
            }

            if ($shopOwner) {
                Notification::send($shopOwner, new NewOrderNotification($order));
                Log::info('Notifikasi pesanan baru dikirim ke mitra.', ['order_id' => $order->id, 'partner_id' => $shopOwner->id]);
            }

        } catch (\Exception $e) {
            Log::error('FinalizeSingleProductOrder: Gagal mengirim notifikasi.', [
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

        $subdomain = $user->subdomain; // Asumsi relasi 'subdomain' ada di model User
        if ($subdomain) {
            $subdomain->update(['status' => 'active']);
            Log::info('ActivateSubscription: Status subdomain berhasil diupdate menjadi active.', ['subdomain_id' => $subdomain->id]);
        } else {
            Log::warning('ActivateSubscription: Subdomain tidak ditemukan untuk user.', ['user_id' => $user->id]);
        }

        $user->removeRole('calon-mitra');
        $user->assignRole('mitra');

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
