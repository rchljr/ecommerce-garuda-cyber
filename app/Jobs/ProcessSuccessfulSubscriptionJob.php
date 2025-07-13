<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Notification;
use App\Notifications\PartnerActivatedNotification;

class ProcessSuccessfulSubscriptionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (!$this->user || !$this->user->userPackage) {
            Log::error('ProcessSuccessfulSubscriptionJob: User atau UserPackage tidak valid.', ['user_id' => $this->user->id ?? null]);
            return;
        }

        if ($this->user->hasRole('mitra')) {
            Log::info('ProcessSuccessfulSubscriptionJob: Aktivasi dihentikan karena user sudah menjadi mitra.', ['user_id' => $this->user->id]);
            return;
        }

        $userPackage = $this->user->userPackage;
        $activeDate = Carbon::now();
        $expiredDate = $userPackage->plan_type === 'yearly' ? $activeDate->copy()->addYear() : $activeDate->copy()->addMonth();

        $userPackage->update(['status' => 'active', 'active_date' => $activeDate, 'expired_date' => $expiredDate]);
        
        $this->user->removeRole('calon-mitra');
        $this->user->assignRole('mitra');

        if ($this->user->subdomain) {
            $this->user->subdomain->update(['status' => 'active']);
        }

        try {
            Notification::send($this->user, new PartnerActivatedNotification($this->user));
            Log::info('ProcessSuccessfulSubscriptionJob: Aktivasi akun dan notifikasi berhasil.', ['user_id' => $this->user->id]);
        } catch (\Exception $e) {
            Log::error('ProcessSuccessfulSubscriptionJob: Gagal mengirim notifikasi aktivasi.', [
                'user_id' => $this->user->id, 'error' => $e->getMessage()
            ]);
        }
    }
}
