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
// Kita tidak menggunakan MidtransNotification secara langsung di sini untuk tes
// use Midtrans\Notification as MidtransNotification;

class MidtransWebhookService
{
    /**
     * PERBAIKAN: Tipe argumen diubah dari 'MidtransNotification' menjadi 'object'
     * Ini memungkinkan kita mengirim data "palsu" dari Postman untuk pengujian.
     *
     * @param object $notification
     */
    public function handle(object $notification): void
    {
        Log::info('Midtrans Webhook: Notifikasi diterima.', (array) $notification);

        $order = Order::find($notification->order_id);

        if (!$order) {
            Log::warning('Midtrans Webhook: Order tidak ditemukan.', ['order_id' => $notification->order_id]);
            return;
        }

        if (in_array($notification->transaction_status, ['settlement', 'capture'])) {
            Log::info('Midtrans Webhook: Status pembayaran sukses, memproses aktivasi.', ['order_id' => $order->id]);
            $this->handleSuccessfulPayment($order, $notification);
        }
    }

    /**
     * Tipe argumen notifikasi juga diubah menjadi 'object' di sini.
     */
    protected function handleSuccessfulPayment(Order $order, object $notification)
    {
        if ($order->status === 'completed') {
            Log::info('Midtrans Webhook: Order sudah pernah diproses.', ['order_id' => $order->id]);
            return;
        }

        DB::transaction(function () use ($order, $notification) {
            try {
                // 1. Buat catatan pembayaran
                Payment::create([
                    'user_id' => $order->user_id,
                    'order_id' => $order->id,
                    'subs_package_id' => $order->user->userPackage->subs_package_id,
                    'midtrans_order_id' => $notification->order_id,
                    'midtrans_transaction_status' => $notification->transaction_status,
                    'midtrans_payment_type' => $notification->payment_type,
                    'total_payment' => $notification->gross_amount,
                    // Menggunakan json_encode langsung karena objek pengujian kita
                    // tidak memiliki metode getResponse().
                    'midtrans_response' => json_encode($notification),
                ]);
                Log::info('Midtrans Webhook: Catatan pembayaran berhasil dibuat.', ['order_id' => $order->id]);

                // 2. Update status order
                $order->update(['status' => 'completed']);
                Log::info('Midtrans Webhook: Status order berhasil diupdate.', ['order_id' => $order->id]);

                // 3. Aktivasi Akun
                $this->activateSubscription($order->user);

            } catch (\Exception $e) {
                Log::error('Midtrans Webhook: Gagal saat memproses pembayaran sukses.', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }
        });
    }

    protected function activateSubscription(User $user)
    {
        if ($user->hasRole('mitra')) {
            return;
        }

        $userPackage = $user->userPackage;
        $activeDate = Carbon::now();
        $expiredDate = $userPackage->plan_type === 'yearly' ? $activeDate->copy()->addYear() : $activeDate->copy()->addMonth();

        $userPackage->update(['status' => 'active', 'active_date' => $activeDate, 'expired_date' => $expiredDate]);

        $user->update(['status' => 'active']);
        $user->removeRole('calon-mitra');
        $user->assignRole('mitra');

        if ($user->subdomain) {
            $user->subdomain->update(['status' => 'active']);
        }

        Notification::send($user, new PartnerActivatedNotification($user));
        Log::info('Midtrans Webhook: Aktivasi User, Role, dan Subdomain berhasil.', ['user_id' => $user->id]);
    }
}
