<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PartnerRejectedNotification extends Notification
{
    use Queueable;

    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $url = route('register.form');

        return (new MailMessage)
            ->subject('Informasi Verifikasi Pendaftaran Mitra')
            ->greeting('Halo, ' . $this->user->name . '.')
            ->line('Setelah melakukan peninjauan, dengan berat hati kami sampaikan bahwa pengajuan pendaftaran mitra Anda saat ini belum dapat kami setujui.')
            ->line('Silahkan lengkapi persyaratan yang diperlukan atau perbaiki informasi yang kurang tepat pada profil Anda.')
            ->line('Dan silahkan ajukan kembali pendaftaran mitra Anda setelah melakukan perbaikan.')
            ->action( 'Ajukan Ulang Pendaftaran', $url)
            ->line('Jika Anda memiliki pertanyaan lebih lanjut, silakan hubungi tim support kami.')
            ->line('Terima kasih atas pengertian Anda.');
    }
}