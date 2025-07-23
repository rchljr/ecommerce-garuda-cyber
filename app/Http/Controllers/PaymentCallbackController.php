<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Varian; // PENTING: Import model Varian
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PaymentCallbackController extends Controller
{
    public function receiveCallback(Request $request)
    {
        $callbackData = $request->all();

        Log::info('Midtrans Callback Received: ', $callbackData);

        // TODO: Lakukan verifikasi signature hash/authentifikasi notifikasi dari Midtrans (SANGAT PENTING!)
        // Ini memastikan callback berasal dari Midtrans dan tidak dimanipulasi.
        // Anda bisa mendapatkan serverKey dari config/services.php
        // $serverKey = config('services.midtrans.server_key');
        // $hashed = hash('sha512', $callbackData['order_id'] . $callbackData['status_code'] . $callbackData['gross_amount'] . $serverKey);
        // if ($hashed != $callbackData['signature_key']) {
        //     Log::warning("Invalid signature key for order: {$callbackData['order_id']}");
        //     return response('Invalid signature', 400);
        // }

        DB::beginTransaction(); // Mulai transaksi database

        try {
            $orderId = $callbackData['order_id'];
            $transactionStatus = $callbackData['transaction_status'];
            $fraudStatus = $callbackData['fraud_status'];

            $order = Order::where('id', $orderId)->first();
            $payment = Payment::where('order_id', $orderId)->first();

            if (!$order || !$payment) {
                Log::warning("Order or Payment not found for callback order_id: {$orderId}");
                return response('Order or Payment not found', 404);
            }

            // Simpan status lama sebelum update
            $oldOrderStatus = $order->status;

            // Update status pembayaran di tabel payments
            $payment->midtrans_transaction_status = $transactionStatus;
            // Anda juga bisa menyimpan detail lain seperti payment_type, transaction_id, etc.
            $payment->save();

            // Logika pembaruan status order berdasarkan transaction_status Midtrans
            if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
                if ($fraudStatus == 'accept') {
                    // Pembayaran berhasil dan dana sudah masuk
                    if ($order->status == Order::STATUS_PENDING) { // Hanya update jika statusnya masih pending
                        $order->status = Order::STATUS_PROCESSING; // Ubah ke 'Diproses'
                        $order->save();

                        // --- PENTING: DEKREMEN STOK DI SINI ---
                        // Hanya lakukan jika status order berubah dari PENDING ke PROCESSING/COMPLETED
                        // untuk menghindari double decrement jika callback diterima berkali-kali
                        if ($oldOrderStatus == Order::STATUS_PENDING || $oldOrderStatus == Order::STATUS_FAILED) {
                            $order->load('items.variant'); // Eager load varian untuk decrement stok
                            foreach ($order->items as $item) {
                                if ($item->variant) {
                                    if ($item->variant->stock >= $item->quantity) {
                                        $item->variant->decrement('stock', $item->quantity);
                                        Log::info("Stok varian {$item->variant->id} dikurangi sebanyak {$item->quantity}. Stok baru: {$item->variant->stock}");
                                    } else {
                                        // Ini adalah kondisi error: stok tidak cukup padahal pembayaran berhasil
                                        // Anda perlu memutuskan bagaimana menanganinya (misal: batalkan order, notifikasi admin)
                                        Log::error("Stok varian {$item->variant->id} TIDAK CUKUP untuk order {$order->id}. Sisa: {$item->variant->stock}, Dibutuhkan: {$item->quantity}");
                                        // Opsional: throw new Exception("Stock not sufficient for order items.");
                                        // Jika throw exception, transaksi akan rollback.
                                    }
                                } else {
                                    Log::warning("Varian tidak ditemukan untuk OrderItem ID: {$item->id} dari Order ID: {$order->id}. Stok tidak dikurangi.");
                                }
                            }
                        }
                    }
                } else {
                    // Fraud / Pembayaran bermasalah
                    $order->status = Order::STATUS_FAILED;
                    $order->save();
                    Log::warning("Order {$orderId} marked as FAILED due to fraud status: {$fraudStatus}.");
                }
            } elseif ($transactionStatus == 'pending') {
                $order->status = Order::STATUS_PENDING; // Tetap pending
                $order->save();
            } elseif ($transactionStatus == 'deny' || $transactionStatus == 'cancel' || $transactionStatus == 'expire') {
                $order->status = Order::STATUS_CANCELLED; // Atau FAILED
                $order->save();
                Log::info("Order {$orderId} marked as CANCELLED/DENIED/EXPIRED due to transaction status: {$transactionStatus}.");
                // TODO: Jika stok sudah terreserved sebelumnya (misal di awal checkout), kembalikan stoknya di sini
            } elseif ($transactionStatus == 'refund' || $transactionStatus == 'partial_refund') {
                $order->status = Order::STATUS_REFUNDED;
                $order->save();
                Log::info("Order {$orderId} marked as REFUNDED due to transaction status: {$transactionStatus}.");
                // TODO: Jika refund, perbarui stok sesuai kebijakan Anda
            }
            // Tambahkan kondisi untuk status Midtrans lainnya jika ada (e.g., authorize, settle, etc.)

            DB::commit(); // Commit transaksi jika berhasil
            Log::info("Order {$orderId} status updated to {$order->status} via Midtrans callback. Payment status: {$payment->midtrans_transaction_status}.");
            return response('Callback Success', 200);

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaksi jika terjadi error
            Log::error("Error processing Midtrans callback for order {$orderId}: " . $e->getMessage(), ['exception' => $e, 'callback_data' => $callbackData]);
            return response('Callback Error', 500);
        }
    }
}