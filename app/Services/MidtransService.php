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
     * @return object|null Respons dari Midtrans dalam bentuk object atau null jika gagal.
     * @throws \Exception Jika terjadi error API yang tidak terduga.
     */
    public function cancelTransaction(string $midtransOrderId): ?object
    {
        try {
            Log::info("MidtransService: Mencoba membatalkan transaksi.", ['midtrans_order_id' => $midtransOrderId]);

            $responseString = Transaction::cancel($midtransOrderId);

            $responseValue = json_decode($responseString);

            // [FIX] Tambahkan pengecekan untuk memastikan nilai yang dikembalikan adalah object.
            // Jika Midtrans mengembalikan nilai yang di-decode menjadi integer atau tipe lain,
            // kita akan menganggapnya sebagai respons yang tidak standar dan mengembalikan null.
            if (!is_object($responseValue)) {
                Log::warning("MidtransService: Respons dari pembatalan transaksi bukanlah sebuah object.", [
                    'midtrans_order_id' => $midtransOrderId,
                    'decoded_type' => gettype($responseValue),
                    'original_response' => $responseString
                ]);
                // Kembalikan null untuk memenuhi return type hint (?object) dan menandakan
                // bahwa respons tidak dapat diproses lebih lanjut, meskipun tidak ada exception.
                return null;
            }

            Log::info("MidtransService: Berhasil membatalkan transaksi.", [
                'midtrans_order_id' => $midtransOrderId,
                'response' => $responseValue
            ]);

            return $responseValue;

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
