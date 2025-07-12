<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomerPaymentSuccessNotification extends Notification
{
    use Queueable;

    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $destinationUrl = route('tenant.account.orders', ['subdomain' => $this->order->subdomain->subdomain_name]);
        $loginUrl = route('tenant.customer.login.form', [
            'subdomain' => $this->order->subdomain->subdomain_name,
            'redirect' => $destinationUrl
        ]);

        $mailMessage = (new MailMessage)
            ->subject('Pembayaran Berhasil untuk Pesanan #' . $this->order->id)
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Terima kasih! Kami telah menerima pembayaran Anda untuk pesanan dengan nomor #' . $this->order->id . '.');

        if ($this->order->shipping) {
            // Jika ada, berarti metode adalah 'Kirim ke Alamat'
            $mailMessage->line('Pesanan Anda sedang kami proses dan akan segera kami kirimkan ke alamat Anda.');
        } else {
            // Jika tidak ada, berarti metode adalah 'Ambil di Toko'
            $mailMessage->line('Pesanan Anda sedang kami siapkan. Silahkan untuk diambil di lokasi toko.');
        }

        $mailMessage->action('Lihat Detail Pesanan', $loginUrl)
            ->line('Terima kasih telah berbelanja di toko kami.');

        return $mailMessage;
    }
}
