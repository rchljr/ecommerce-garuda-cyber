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

    public function __construct(RegistrationService $registrationService)
    {
        $this->registrationService = $registrationService;
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
            $user->load('userPackage.subscriptionPackage');

            $userPackage = $user->userPackage;
            $package = optional($userPackage)->subscriptionPackage;

            if (!$package) {
                throw new Exception("Paket langganan tidak ditemukan untuk user ID: {$user->id}");
            }

            // ---  Cek apakah paketnya trial ---
            if ($package->is_trial) {
                // Jika TRIAL, panggil metode untuk mengaktifkan semuanya
                $this->registrationService->activateTrialPackage($user, $package);
            } else {
                // Jika BERBAYAR, hanya ubah status user agar bisa lanjut bayar
                $user->update(['status' => 'active']);
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
