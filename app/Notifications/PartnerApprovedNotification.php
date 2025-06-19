<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PartnerApprovedNotification extends Notification
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
        // Link pembayaran akan mengarahkan user ke halaman login lalu ke pembayaran
        $paymentUrl = route('login'); // User harus login dulu sebelum membayar

        return (new MailMessage)
            ->subject('Selamat, Pengajuan Mitra Anda Disetujui!')
            ->greeting('Halo, ' . $this->user->name . '!')
            ->line('Kabar baik! Pengajuan pendaftaran mitra Anda telah kami verifikasi dan setujui.')
            ->line('Langkah selanjutnya adalah menyelesaikan pembayaran untuk mengaktifkan paket Anda.')
            ->action('Lanjutkan ke Pembayaran', $paymentUrl)
            ->line('Terima kasih telah bergabung dengan kami.');
    }
}