<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use App\Notifications\NewOrderNotification;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Notification;
use App\Notifications\CustomerPaymentSuccessNotification;

class ProcessSuccessfulOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Update status pengiriman jika ada
        if ($this->order->shipping) {
            $this->order->shipping->update(['status' => 'preparing_shipment']);
        }

        try {
            $customer = $this->order->user;
            $shopOwner = $this->order->subdomain->user;

            // Kirim notifikasi ke pelanggan
            Notification::send($customer, new CustomerPaymentSuccessNotification($this->order));
            Log::info('Notifikasi sukses pembayaran dikirim ke pelanggan.', ['order_id' => $this->order->id]);

            // Kirim notifikasi ke mitra (pemilik toko)
            Notification::send($shopOwner, new NewOrderNotification($this->order));
            Log::info('Notifikasi pesanan baru dikirim ke mitra.', ['order_id' => $this->order->id]);

        } catch (\Exception $e) {
            Log::error('ProcessSuccessfulOrderJob: Gagal mengirim notifikasi.', [
                'order_id' => $this->order->id, 'error' => $e->getMessage()
            ]);
        }
    }
}
