<?php

namespace App\Services;

use Exception;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\PartnerApprovedNotification;
use App\Notifications\PartnerRejectedNotification;

class VerificationService
{
    protected $registrationService;
    protected $orderService;

    public function __construct(RegistrationService $registrationService, OrderService $orderService)
    {
        $this->registrationService = $registrationService;
        $this->orderService = $orderService;
    }
    /**
     * Mengambil semua pengguna dengan role 'calon-mitra' yang statusnya 'pending'.
     */
    public function getPendingPartners()
    {
        return User::role('calon-mitra')
            ->where('status', 'pending')
            ->with(['shop', 'subdomain', 'userPackage'])
            ->latest()
            ->get();
    }

    /**
     * Menyetujui pendaftaran seorang mitra.
     */
    public function approvePartner(User $user)
    {
        DB::transaction(function () use ($user) {
            $user->load('userPackage.subscriptionPackage', 'subdomain'); // Muat relasi subdomain juga

            $userPackage = $user->userPackage;
            $package = optional($userPackage)->subscriptionPackage;

            if (!$package) {
                throw new Exception("Paket langganan tidak ditemukan untuk user ID: {$user->id}");
            }

            // --- Cek apakah paketnya trial ---
            if ($package->is_trial) {
                // Buat pesanan terlebih dahulu untuk mencatat transaksi trial (meskipun gratis)
                $this->orderService->createSubscriptionOrder($user);

                // [PERBAIKAN] Ambil subdomain dari user dan teruskan ke service
                $subdomain = $user->subdomain;
                if (!$subdomain) {
                    throw new Exception("Subdomain tidak ditemukan untuk user ID: {$user->id}");
                }

                // Panggil metode dengan tiga argumen yang benar: User, Package, dan Subdomain
                $this->registrationService->activateTrialPackage($user, $package, $subdomain);
            } else {
                // Jika BERBAYAR, hanya ubah status user agar bisa lanjut bayar
                $user->update(['status' => 'active']);
                // Buat pesanan setelah status diubah
                $this->orderService->createSubscriptionOrder($user);
                Notification::send($user, new PartnerApprovedNotification($user));
            }
        });
    }

    /**
     * Menolak dan menghapus pendaftaran seorang mitra.
     */
    public function rejectPartner(User $user)
    {
        DB::transaction(function () use ($user) {
            $user->update(['status' => 'inactive']);
            // 1. Kirim notifikasi email penolakan terlebih dahulu
            Notification::send($user, new PartnerRejectedNotification($user));

            // 2. Hapus data user. Relasi (shop, subdomain, dll) akan terhapus otomatis
            //    jika Anda menggunakan onDelete('cascade') pada migrasi.
            $user->delete();
        });
    }
}
