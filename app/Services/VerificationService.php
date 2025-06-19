<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\PartnerApprovedNotification;
use App\Notifications\PartnerRejectedNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class VerificationService
{
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
            // 1. Ubah status dan role user
            $user->update([
                'status' => 'active', // Status user disetujui, siap untuk bayar
            ]);

            // 2. Kirim notifikasi email persetujuan
            Notification::send($user, new PartnerApprovedNotification($user));
        });
    }

    /**
     * Menolak dan menghapus pendaftaran seorang mitra.
     */
    public function rejectPartner(User $user)
    {
        DB::transaction(function () use ($user) {
            // 1. Kirim notifikasi email penolakan terlebih dahulu
            Notification::send($user, new PartnerRejectedNotification($user));

            // 2. Hapus data user. Relasi (shop, subdomain, dll) akan terhapus otomatis
            //    jika Anda menggunakan onDelete('cascade') pada migrasi.
            $user->delete();
        });
    }
}
