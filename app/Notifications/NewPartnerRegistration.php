<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewPartnerRegistration extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Instance dari user yang baru mendaftar.
     *
     * @var \App\Models\User
     */
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
        // PERBAIKAN: Pastikan URL ini mengarah ke halaman daftar verifikasi (index),
        // bukan ke aksi approve secara langsung.
        $verificationUrl = route('admin.mitra.verifikasi');

        return (new MailMessage)
            ->subject('Pendaftaran Mitra Baru Perlu Verifikasi')
            ->greeting('Halo Admin,')
            ->line('Seorang calon mitra baru telah mendaftar dan membutuhkan verifikasi dari Anda.')
            ->line('Berikut adalah detail pendaftar:')
            ->line('**Nama:** ' . $this->user->name)
            ->line('**Email:** ' . $this->user->email)
            ->action('Lihat Detail & Verifikasi', $verificationUrl)
            ->line('Terima kasih.');
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
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'message' => 'Pendaftaran mitra baru menunggu verifikasi.',
        ];
    }
}
