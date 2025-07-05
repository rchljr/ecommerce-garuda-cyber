<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Notifications\PartnerActivatedNotification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Midtrans\Notification as MidtransNotification;

class MidtransWebhookService
{
    /**
     * Menangani notifikasi webhook dari Midtrans dengan validasi dan logging yang detail.
     */
    public function handle(MidtransNotification $notification): void
    {
        Log::info('MidtransWebhookService: Service handle dipanggil.', ['order_id_midtrans' => $notification->order_id]);

        // 1. Validasi Tipe Transaksi
        if ($notification->transaction_status !== 'settlement' && $notification->transaction_status !== 'capture') {
            Log::info('MidtransWebhookService: Status transaksi bukan settlement atau capture, diabaikan.', ['status' => $notification->transaction_status]);
            return;
        }

        // 2. Ekstrak ID Order Asli dengan Aman
        $transactionId = $notification->order_id;
        $lastHyphenPos = strrpos($transactionId, '-');
        if ($lastHyphenPos === false) {
            Log::warning('MidtransWebhookService: Format order_id dari Midtrans tidak valid.', ['order_id' => $transactionId]);
            return;
        }
        $originalOrderId = substr($transactionId, 0, $lastHyphenPos);
        Log::info('MidtransWebhookService: ID Order asli berhasil diekstrak.', ['original_order_id' => $originalOrderId]);

        // 3. Cari Order dan Lakukan Validasi Ketat
        $order = Order::with('user.userPackage')->find($originalOrderId);

        if (!$order) {
            Log::warning('MidtransWebhookService: Order dengan ID asli tidak ditemukan di database.', ['original_order_id' => $originalOrderId]);
            return;
        }

        if ($order->status === 'completed') {
            Log::info('MidtransWebhookService: Order ini sudah pernah diproses (status completed).', ['order_id' => $order->id]);
            return;
        }

        // Validasi tambahan untuk memastikan relasi ada sebelum masuk ke transaksi DB
        if (!$order->user) {
            Log::error('MidtransWebhookService: Relasi User pada Order tidak ditemukan.', ['order_id' => $order->id]);
            return;
        }
        if (!$order->user->userPackage) {
            Log::error('MidtransWebhookService: Relasi UserPackage pada User tidak ditemukan.', ['user_id' => $order->user->id]);
            return;
        }

        // 4. Lakukan Proses dalam Transaksi Database
        $this->processSuccessfulPayment($order, $notification);
    }

    /**
     * Memproses pembayaran yang sukses di dalam transaksi DB.
     */
    protected function processSuccessfulPayment(Order $order, MidtransNotification $notification)
    {
        DB::transaction(function () use ($order, $notification) {
            try {
                // Langkah A: Buat Catatan Pembayaran
                Payment::create([
                    'user_id' => $order->user_id,
                    'order_id' => $order->id,
                    'subs_package_id' => $order->user->userPackage->subs_package_id,
                    'midtrans_order_id' => $notification->transaction_id,
                    'midtrans_transaction_status' => $notification->transaction_status,
                    'midtrans_payment_type' => $notification->payment_type,
                    'total_payment' => $notification->gross_amount,
                    'midtrans_response' => $notification->getResponse(),
                ]);
                Log::info('MidtransWebhookService: Catatan pembayaran berhasil dibuat.', ['order_id' => $order->id]);

                // Langkah B: Update Status Order
                $order->update(['status' => 'completed']);
                Log::info('MidtransWebhookService: Status order berhasil diupdate menjadi completed.', ['order_id' => $order->id]);

                // Langkah C: Aktivasi Akun
                $this->activateSubscription($order->user);

            } catch (\Exception $e) {
                Log::critical('MidtransWebhookService: GAGAL TOTAL saat memproses transaksi database.', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                // Melempar kembali exception akan otomatis me-rollback transaksi DB.
                throw $e;
            }
        });
    }

    /**
     * Mengaktifkan langganan dan mengubah role pengguna.
     */
    protected function activateSubscription(User $user)
    {
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
        Log::info('MidtransWebhookService: Aktivasi User, Role, dan Subdomain berhasil.', ['user_id' => $user->id]);
    }
}
