<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MitraDeactivatedNotification extends Notification implements ShouldQueue
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
        return ['mail']; // Kirim notifikasi ini melalui email
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        // Anda bisa menambahkan URL ke halaman kontak admin jika ada
        // $contactUrl = route('contact.admin'); 

        return (new MailMessage)
            ->subject('Pemberitahuan Penonaktifan Akun Mitra')
            ->greeting('Halo, ' . $this->user->name . '!')
            ->line('Dengan berat hati kami memberitahukan bahwa akun mitra Anda telah dinonaktifkan sementara oleh administrator kami.')
            ->line('Penonaktifan ini dilakukan karena terindikasi adanya pelanggaran terhadap syarat dan ketentuan penggunaan platform kami. Toko Anda tidak akan dapat diakses oleh pelanggan selama status akun nonaktif.')
            ->line('Jika Anda merasa ini adalah sebuah kekeliruan atau ingin mendiskusikan hal ini lebih lanjut, silakan hubungi tim support kami.')
            // ->action('Hubungi Admin', $contactUrl) // Uncomment baris ini jika Anda punya halaman kontak admin
            ->line('Terima kasih atas pengertian Anda.');
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
