<?php

namespace App\Http\Controllers;

use Exception;
use Midtrans\Snap;
use Midtrans\Config;
use App\Models\Order;
use Midtrans\CoreApi;
use App\Models\Payment;
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
        // Hapus session registrasi lama saat pengguna sampai di halaman pembayaran
        $this->multiStep->clear();
        session()->forget('newly_registered_user_id');

        $user = Auth::user();
        $order = Order::with('userPackage.subscriptionPackage', 'voucher')
            ->where('user_id', $user->id)
            ->whereIn('status', ['pending', 'waiting_payment', 'failed', 'cancelled']) // Tambahkan status gagal/batal
            ->latest()
            ->first();

        if (!$order) {
            return redirect()->route('mitra.dashboard')->with('info', 'Anda tidak memiliki tagihan yang perlu dibayar.');
        }

        // Jalankan kalkulasi harga setiap kali halaman dimuat
        $this->recalculatePrices($order);
        
        // Jika order gagal/batal, reset statusnya ke pending agar bisa coba bayar lagi
        if (in_array($order->status, ['failed', 'cancelled'])) {
            $order->update(['status' => 'pending']);
            // Hapus juga catatan pembayaran lama yang gagal agar bisa buat baru
            Payment::where('order_id', $order->id)->delete();
        }

        // Cek apakah sudah ada instruksi pembayaran yang aktif untuk order ini
        $pendingPayment = Payment::where('order_id', $order->id)
            ->where('midtrans_transaction_status', 'pending')
            ->first();

        // Jika sudah ada, langsung tampilkan instruksi yang ada
        if ($pendingPayment && $pendingPayment->midtrans_response) {
            $paymentDetails = json_decode($pendingPayment->midtrans_response);
            return view('landing-page.auth.partials._step5', [
                'order' => $order,
                'pendingPayment' => $paymentDetails
            ]);
        }

        return view('landing-page.auth.partials._step5', ['order' => $order]);
    }
    /**
     * Membuat sesi pembayaran dan MENYIMPAN instruksinya.
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

        $finalPrice = round($finalPrice);
        if ($finalPrice < 0)
            $finalPrice = 0;

        $order->total_price = $finalPrice;
        $order->save();

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
            case 'bni_va':
            case 'bri_va':
                $params['payment_type'] = 'bank_transfer';
                $params['bank_transfer'] = ['bank' => str_replace('_va', '', $request->payment_method)];
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

            Payment::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'user_id' => $order->user_id,
                    'subs_package_id' => $order->user->userPackage->subs_package_id,
                    'midtrans_order_id' => $response->order_id,
                    // 'midtrans_transaction_id' => $response->transaction_id,
                    'midtrans_transaction_status' => $response->transaction_status,
                    'midtrans_payment_type' => $response->payment_type,
                    'total_payment' => $response->gross_amount,
                    'midtrans_response' => json_encode($response),
                ]
            );

            return response()->json($response);
        } catch (Exception $e) {
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

        $order->voucher_id = $voucher->id;
        $order->save();

        // Hitung ulang harga setelah voucher diterapkan
        $recalculatedData = $this->recalculatePrices($order);

        return response()->json([
            'success' => true,
            'message' => 'Voucher berhasil diterapkan!',
            'final_price' => $recalculatedData['final_price'],
            'discount_amount' => $recalculatedData['voucher_discount_amount'],
            'voucher_code' => $voucher->voucher_code
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

        $order->voucher_id = null;
        $order->save();

        // Hitung ulang harga setelah voucher dihapus
        $recalculatedData = $this->recalculatePrices($order);

        return response()->json([
            'success' => true,
            'message' => 'Voucher berhasil dihapus.',
            'final_price' => $recalculatedData['final_price']
        ]);
    }

    /**
     * Fungsi privat untuk sentralisasi kalkulasi harga.
     */
    private function recalculatePrices(Order $order): array
    {
        $userPackage = $order->userPackage;
        $subscriptionPackage = $userPackage->subscriptionPackage;

        $originalPrice = ($userPackage->plan_type === 'yearly')
            ? $subscriptionPackage->yearly_price
            : $subscriptionPackage->monthly_price;
        
        $yearlyDiscountAmount = 0;
        if ($userPackage->plan_type === 'yearly') {
            $discountPercentage = $subscriptionPackage->discount_year ?? 0;
            $yearlyDiscountAmount = ($originalPrice * $discountPercentage) / 100;
        }

        $priceAfterYearlyDiscount = $originalPrice - $yearlyDiscountAmount;

        $voucherDiscountAmount = 0;
        if ($order->voucher_id) {
            $voucher = Voucher::find($order->voucher_id);
            if ($voucher) {
                // Pastikan voucher memenuhi syarat belanja (dihitung dari harga setelah diskon tahunan)
                if ($priceAfterYearlyDiscount >= $voucher->min_spending) {
                    $voucherDiscountPercentage = $voucher->discount ?? 0;
                    $voucherDiscountAmount = ($priceAfterYearlyDiscount * $voucherDiscountPercentage) / 100;
                } else {
                    // Jika tidak memenuhi syarat, hapus voucher dari order
                    $order->voucher_id = null;
                }
            }
        }

        $finalPrice = $priceAfterYearlyDiscount - $voucherDiscountAmount;
        
        // Update order dengan harga terbaru
        $order->total_price = round($finalPrice);
        $order->save();

        return [
            'final_price' => $order->total_price,
            'voucher_discount_amount' => $voucherDiscountAmount,
        ];
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
