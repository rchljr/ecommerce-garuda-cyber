<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PackageDeactivatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public User $user)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $packageName = optional($this->user->userPackage->subscriptionPackage)->package_name ?? 'Anda';
        $reactivateUrl = route('register.form');

        return (new MailMessage)
            ->subject('Paket Langganan Anda Telah Dinonaktifkan')
            ->greeting('Halo, ' . $this->user->name . '.')
            ->error() // Memberi warna merah pada email untuk menandakan urgensi
            ->line('Kami informasikan bahwa Paket **' . $packageName . '** Anda telah kedaluwarsa dan saat ini berstatus **Tidak Aktif**.')
            ->line('Akibatnya, akses Anda ke fitur-fitur premium dan toko online Anda telah kami nonaktifkan untuk sementara.')
            ->line('Jangan khawatir, semua data Anda masih tersimpan dengan aman.')
            ->action('Aktifkan Kembali Sekarang', $reactivateUrl)
            ->line('Dengan mengaktifkan kembali, Anda dapat langsung menikmati semua layanan seperti sedia kala.')
            ->line('Jika Anda tidak melakukan pengaktifan kembali dalam waktu 1 bulan, akun Anda akan dihapus secara permanen sesuai kebijakan kami.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
