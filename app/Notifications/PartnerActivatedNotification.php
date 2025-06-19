<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PartnerActivatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $user;

    /**
     * Buat instance notifikasi baru.
     *
     * @param \App\Models\User $user
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Tentukan channel pengiriman notifikasi.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Buat representasi email dari notifikasi.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        // Pastikan Anda memiliki helper 'format_tanggal'
        $expiredDateFormatted = format_tanggal($this->user->userPackage->expired_date);

        // Arahkan ke halaman login
        $loginUrl = route('login');

        return (new MailMessage)
            ->subject('Akun Mitra Anda Telah Aktif!')
            ->greeting('Selamat Datang, ' . $this->user->name . '!')
            ->line('Pembayaran Anda telah berhasil kami verifikasi. Akun mitra Anda sekarang sudah aktif sepenuhnya.')
            ->line('Subdomain Anda: **' . $this->user->subdomain->subdomain_name . '.garuda.id**') // Ganti '.garuda.id' jika perlu
            ->line('Paket Anda akan aktif hingga: **' . $expiredDateFormatted . '**')
            ->action('Masuk ke Dashboard Anda', $loginUrl)
            ->line('Terima kasih telah bergabung bersama kami!');
    }

    /**
     * Dapatkan representasi array dari notifikasi.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            // Bisa digunakan untuk notifikasi di dalam aplikasi
        ];
    }
}