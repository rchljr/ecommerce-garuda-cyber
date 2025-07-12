<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification
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
        $url = route('mitra.orders.show', $this->order->id); // Sesuaikan dengan rute detail order di dasbor mitra

        return (new MailMessage)
            ->subject('Pesanan Baru Telah Diterima! #' . $this->order->id)
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Anda baru saja menerima pesanan baru dari pelanggan ' . $this->order->user->name . '.')
            ->line('Total pesanan: ' . format_rupiah($this->order->total_price))
            ->action('Lihat dan Proses Pesanan', $url)
            ->line('Harap segera proses pesanan ini. Terima kasih!');
    }
}
