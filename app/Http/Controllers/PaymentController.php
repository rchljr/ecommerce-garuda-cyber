<?php

namespace App\Http\Controllers;

use Exception;
use Midtrans\Snap;
use Midtrans\Config;
use App\Models\Order;
use Midtrans\CoreApi;
use App\Models\Voucher;
use Midtrans\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Services\MidtransWebhookService;
use App\Services\MultiStepRegistrationService;

class PaymentController extends Controller
{
    protected $multiStep;

    // 2. Inject service melalui constructor
    public function __construct(MultiStepRegistrationService $multiStep)
    {
        $this->multiStep = $multiStep;
        // Setup Midtrans Config di constructor agar lebih rapi
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
    }

    /**
     * Menampilkan halaman pembayaran untuk pengguna yang sudah login.
     */
    public function show()
    {
        $this->multiStep->clear();
        session()->forget('newly_registered_user_id');

        $user = Auth::user();
        $order = Order::with('userPackage.subscriptionPackage', 'voucher')
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->latest()
            ->first();

        if (!$order) {
            return redirect()->route('mitra.dashboard')->with('info', 'Anda tidak memiliki tagihan yang perlu dibayar.');
        }

        return view('landing-page.auth.partials._step5', ['order' => $order]);
    }
    /**
     * Method  yang paling penting untuk menangani pembayaran via Core API.
     */
    public function chargeCoreApi(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|string|in:bca_va,bni_va,bri_va,gopay,qris',
        ]);

        $user = Auth::user();
        $order = Order::with('voucher', 'user.userPackage')->where('user_id', $user->id)->where('status', 'pending')->latest()->first();

        if (!$order) {
            return response()->json(['error' => 'Order aktif tidak ditemukan.'], 404);
        }

        $finalPrice = $order->user->userPackage->price_paid;

        if ($order->voucher) {
            $percentage = $order->voucher->discount;
            $discountAmount = ($finalPrice * $percentage) / 100;
            $finalPrice -= $discountAmount;
        }

        // PERBAIKAN: Pastikan harga adalah integer (pembulatan) untuk Midtrans.
        $finalPrice = round($finalPrice);
        if ($finalPrice < 0)
            $finalPrice = 0;

        $order->total_price = $finalPrice;
        $order->save();

        // PERBAIKAN: Buat ID transaksi unik untuk setiap percobaan pembayaran.
        $transactionId = $order->id . '-' . time();

        $params = [
            'transaction_details' => [
                'order_id' => $transactionId, // Gunakan ID unik
                'gross_amount' => $order->total_price,
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
            ],
        ];

        switch ($request->payment_method) {
            case 'bca_va':
                $params['payment_type'] = 'bank_transfer';
                $params['bank_transfer'] = ['bank' => 'bca'];
                break;
            case 'bni_va':
                $params['payment_type'] = 'bank_transfer';
                $params['bank_transfer'] = ['bank' => 'bni'];
                break;
            case 'bri_va':
                $params['payment_type'] = 'bank_transfer';
                $params['bank_transfer'] = ['bank' => 'bri'];
                break;
            case 'gopay':
                $params['payment_type'] = 'gopay';
                break;
            case 'qris':
                $params['payment_type'] = 'qris';
                break;
        }

        try {
            $response = CoreApi::charge($params);
            return response()->json($response);
        } catch (Exception $e) {
            // PERBAIKAN: Logging dan pesan error yang lebih detail.
            $errorBody = $e->getMessage();
            $decodedError = json_decode($errorBody);
            $errorMessage = $errorBody;

            if (json_last_error() === JSON_ERROR_NONE && isset($decodedError->error_messages)) {
                $errorMessage = implode(', ', $decodedError->error_messages);
            }

            Log::error("Midtrans Core API Error: " . $errorMessage, [
                'order_id' => $order->id,
                'transaction_id' => $transactionId
            ]);
            return response()->json(['error' => 'Gagal membuat sesi pembayaran: ' . $errorMessage], 500);
        }
    }

    public function applyVoucher(Request $request)
    {
        $request->validate(['voucher_code' => 'required|string']);

        $user = Auth::user();
        $order = Order::where('user_id', $user->id)->where('status', 'pending')->latest()->first();

        if (!$order) {
            return response()->json(['error' => 'Order tidak ditemukan.'], 404);
        }

        $voucher = Voucher::where('voucher_code', $request->voucher_code)
            ->where('start_date', '<=', now())
            ->where('expired_date', '>=', now())
            ->first();

        if (!$voucher) {
            return response()->json(['error' => 'Kode voucher tidak valid atau sudah kedaluwarsa.'], 404);
        }

        $originalPrice = $order->user->userPackage->price_paid;
        if ($originalPrice < $voucher->min_spending) {
            return response()->json(['error' => 'Total belanja tidak memenuhi syarat minimum untuk voucher ini.'], 422);
        }

        // Cek minimum pembelanjaan
        $percentage = $voucher->discount;
        $discountAmount = ($originalPrice * $percentage) / 100;
        $finalPrice = $originalPrice - $discountAmount;
        if ($finalPrice < 0)
            $finalPrice = 0;

        $order->voucher_id = $voucher->id;
        $order->total_price = $finalPrice;
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Voucher berhasil diterapkan!',
            'final_price' => $finalPrice,
            'discount_amount' => $discountAmount,
            'original_price' => $originalPrice
        ]);
    }

    /**
     * Method  untuk menghapus voucher dari order.
     */
    public function removeVoucher(Request $request)
    {
        $user = Auth::user();
        $order = Order::with('user.userPackage')->where('user_id', $user->id)->where('status', 'pending')->latest()->first();

        if (!$order) {
            return response()->json(['error' => 'Order tidak ditemukan.'], 404);
        }

        if (!$order->voucher_id) {
            return response()->json(['error' => 'Tidak ada voucher yang diterapkan.'], 400);
        }

        $originalPrice = $order->user->userPackage->price_paid;

        // Hapus voucher dan kembalikan harga ke semula
        $order->voucher_id = null;
        $order->total_price = $originalPrice;
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Voucher berhasil dihapus.',
            'final_price' => $originalPrice
        ]);
    }

    /**
     * Menggunakan handleWebhook yang sesungguhnya untuk Midtrans.
     */
    public function handleWebhook(Request $request, MidtransWebhookService $webhookService)
    {
        try {
            // Buat instance notifikasi dari Midtrans, ini akan membaca input dari request secara otomatis
            $notification = new Notification();
            
            // Panggil service dengan object notifikasi yang sudah divalidasi
            $webhookService->handle($notification);

            return response()->json(['status' => 'ok', 'message' => 'Webhook processed successfully.']);
            
        } catch (Exception $e) {
            Log::error('Midtrans Webhook Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['status' => 'error', 'message' => 'Webhook Error: ' . $e->getMessage()], 400);
        }
    }
}
