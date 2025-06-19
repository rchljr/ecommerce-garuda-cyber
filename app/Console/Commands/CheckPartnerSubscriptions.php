<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\RenewalReminderNotification;
use App\Notifications\PackageDeactivatedNotification;

class CheckPartnerSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cek status langganan mitra, kirim notifikasi, dan nonaktifkan paket yang kedaluwarsa';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::info('Cron Job (subscriptions:check) mulai berjalan.');
        $today = Carbon::today();

        // 1. Kirim Notifikasi Perpanjangan (H-7)
        $this->sendRenewalReminders($today);

        // 2. Nonaktifkan Paket yang Kedaluwarsa Hari Ini
        $this->deactivateExpiredPackages($today);

        // 3. Hapus Akun yang Sudah Lama Tidak Aktif (lebih dari 1 bulan)
        $this->deleteInactiveAccounts($today);

        Log::info('Cron Job (subscriptions:check) selesai.');
        $this->info('Pengecekan langganan selesai.');
    }

    private function sendRenewalReminders(Carbon $today)
    {
        // Cari paket yang akan berakhir tepat 7 hari dari sekarang
        $targetDate = $today->copy()->addDays(7)->toDateString();

        $usersToRemind = User::role('mitra')
            ->whereHas('userPackage', function ($query) use ($targetDate) {
                $query->where('status', 'active')
                    ->whereDate('expired_date', $targetDate);
            })->get();

        foreach ($usersToRemind as $user) {
            Notification::send($user, new RenewalReminderNotification($user));
            Log::info("Notifikasi perpanjangan (H-7) dikirim ke: {$user->email}");
        }
    }

    private function deactivateExpiredPackages(Carbon $today)
    {
        // Cari paket yang statusnya masih 'active' tapi tanggal kedaluwarsanya adalah kemarin
        // Ini untuk menangkap semua yang sudah melewati masanya
        $expiredPackages = \App\Models\UserPackage::where('status', 'active')
            ->whereDate('expired_date', '<', $today->toDateString())
            ->with('user.subdomain') // Eager load relasi
            ->get();

        foreach ($expiredPackages as $package) {
            // Update status paket
            $package->update(['status' => 'inactive']);

            $user = $package->user;
            if ($user) {
                // Update subdomain jika ada
                if ($user->subdomain) {
                    $user->subdomain->update(['status' => 'inactive']);
                }
                // Kirim notifikasi bahwa paketnya telah dinonaktifkan
                Notification::send($user, new PackageDeactivatedNotification($user));
                Log::info("Paket untuk {$user->email} telah dinonaktifkan karena kedaluwarsa.");
            }
        }
    }

    private function deleteInactiveAccounts(Carbon $today)
    {
        // Cari user yang paketnya sudah 'inactive' dan tanggal kedaluwarsanya lebih dari 1 bulan yang lalu
        $targetDate = $today->copy()->subMonth()->toDateString();

        $usersToDelete = User::role('mitra')
            ->whereHas('userPackage', function ($query) use ($targetDate) {
                $query->where('status', 'inactive')
                    ->whereDate('expired_date', '<=', $targetDate);
            })->get();

        foreach ($usersToDelete as $user) {
            Log::warning("AKUN AKAN DIHAPUS: User ID {$user->id}, Email: {$user->email}. Paket kedaluwarsa lebih dari 1 bulan.");
            // Hapus pengguna. Relasi akan terhapus secara cascade.
            $user->delete();
        }
    }
}