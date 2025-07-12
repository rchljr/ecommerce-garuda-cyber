<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Support\HtmlString;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

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
        $testimonialUrl = route('landing') . '#add-testimonial';

        $testimonialButton = '<a href="' . $testimonialUrl . '" class="button button-primary" style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Helvetica, Arial, sans-serif, \'Apple Color Emoji\', \'Segoe UI Emoji\', \'Segoe UI Symbol\'; position: relative; -webkit-text-size-adjust: none; border-radius: 4px; color: #fff; display: inline-block; overflow: hidden; text-decoration: none; background-color: #22BC66; border-bottom: 8px solid #22BC66; border-left: 18px solid #22BC66; border-right: 18px solid #22BC66; border-top: 8px solid #22BC66;">Isi Testimoni Sekarang</a>';

        return (new MailMessage)
            ->subject('Akun Mitra Anda Telah Aktif!')
            ->greeting('Selamat Datang, ' . $this->user->name . '!')
            ->line('Pembayaran Anda telah berhasil kami verifikasi. Akun mitra Anda sekarang sudah aktif sepenuhnya.')
            ->line('Subdomain Anda: **' . $this->user->subdomain->subdomain_name . '.ecommercegaruda.my.id**')
            ->line('Paket Anda akan aktif hingga: **' . $expiredDateFormatted . '**')
            ->action('Masuk ke Dashboard Anda', $loginUrl)
            ->line('Kami akan sangat senang mendengar pengalaman pertama Anda. Silakan bagikan testimoni Anda untuk membantu kami menjadi lebih baik.')
            ->line(new HtmlString($testimonialButton))
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