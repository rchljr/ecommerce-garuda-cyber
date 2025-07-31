<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MitraReactivatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $user;

    /**
     * Create a new notification instance.
     *
     * @param \App\Models\User $user
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $loginUrl = route('login'); // Ganti dengan rute login mitra jika berbeda

        return (new MailMessage)
            ->subject('Akun Mitra Anda Telah Diaktifkan Kembali')
            ->greeting('Halo, ' . $this->user->name . '!')
            ->line('Kabar baik! Akun mitra Anda telah berhasil diaktifkan kembali oleh administrator.')
            ->line('Toko Anda kini sudah aktif dan silahkan publish ulang agar dapat diakses kembali oleh pelanggan.')
            ->action('Masuk ke Dashboard Anda', $loginUrl)
            ->line('Terima kasih atas kerja sama Anda.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
