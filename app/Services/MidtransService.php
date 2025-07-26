<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Transaction;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    /**
     * Mengatur konfigurasi Midtrans saat service diinisialisasi.
     */
    public function __construct()
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * Mengirim permintaan pembatalan transaksi ke Midtrans.
     *
     * @param string $midtransOrderId ID transaksi Midtrans (misal: PAY-xxxx)
     * @return object|null Respons dari Midtrans atau null jika gagal.
     * @throws \Exception Jika terjadi error API.
     */
    public function cancelTransaction(string $midtransOrderId): ?object
    {
        try {
            Log::info("MidtransService: Mencoba membatalkan transaksi.", ['midtrans_order_id' => $midtransOrderId]);

            // Panggil static method cancel dari library Midtrans
            $response = Transaction::cancel($midtransOrderId);

            Log::info("MidtransService: Berhasil membatalkan transaksi.", ['midtrans_order_id' => $midtransOrderId]);

            return $response;

        } catch (\Exception $e) {
            // Tangani error jika transaksi tidak ditemukan, sudah diselesaikan, atau error lainnya
            Log::error("MidtransService: Gagal membatalkan transaksi.", [
                'midtrans_order_id' => $midtransOrderId,
                'error_code' => $e->getCode(),
                'error_message' => $e->getMessage()
            ]);

            // Lempar kembali exception agar bisa ditangani oleh controller
            throw $e;
        }
    }
}
