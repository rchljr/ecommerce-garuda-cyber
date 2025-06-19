<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RenewalReminderNotification extends Notification implements ShouldQueue
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
        // Ambil data paket untuk ditampilkan di email
        $userPackage = $this->user->userPackage;
        $packageName = optional($userPackage->subscriptionPackage)->package_name ?? 'Anda';
        $expiredDate = optional($userPackage)->expired_date ? \Carbon\Carbon::parse($userPackage->expired_date)->isoFormat('D MMMM YYYY') : 'segera';

        // Arahkan pengguna ke halaman login. Setelah login, sistem akan mengarahkannya ke halaman pembayaran.
        $renewalUrl = route('login');

        return (new MailMessage)
            ->subject('Penting: Paket Langganan Anda Akan Segera Berakhir!')
            ->greeting('Halo, ' . $this->user->name . '!')
            ->line('Ini adalah pengingat bahwa Paket **' . $packageName . '** Anda akan berakhir dalam 7 hari, yaitu pada tanggal **' . $expiredDate . '**.')
            ->line('Untuk memastikan layanan dan toko online Anda tetap aktif tanpa gangguan, silakan lakukan perpanjangan sebelum tanggal tersebut.')
            ->action('Perpanjang Sekarang', $renewalUrl)
            ->line('Jika Anda sudah melakukan perpanjangan, Anda dapat mengabaikan email ini.')
            ->line('Terima kasih telah menjadi bagian dari kami.');
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
