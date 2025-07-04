<?php

namespace App\Http\Controllers;

use Exception;
use Midtrans\Snap;
use Midtrans\Config;
use App\Models\Order;
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
    }

    /**
     * Menampilkan halaman pembayaran untuk pengguna yang sudah login.
     */
    public function show()
    {
        // ===================================================================
        // PERBAIKAN: Hapus session registrasi di sini
        // ===================================================================
        $this->multiStep->clear();
        session()->forget('newly_registered_user_id');
        // ===================================================================

        $user = Auth::user();
        $order = Order::where('user_id', $user->id)
            ->where('status', 'pending')
            ->latest()
            ->first();

        if (!$order) {
            return redirect()->route('mitra.dashboard')->with('info', 'Anda tidak memiliki tagihan yang perlu dibayar.');
        }

        return view('landing-page.partials._step5', [
            'order' => $order,
            // 'snapToken' => $snapToken, // Anda akan men-generate ini nanti
        ]);
    }
    public function generateSnapToken()
    {
        try {
            $user = Auth::user();
            $newOrder = null;

            if (!$user) {
                throw new Exception('User not authenticated.');
            }

            // Gunakan transaction untuk memastikan integritas data
            DB::transaction(function () use ($user, &$newOrder) {
                // 1. Cari order yang masih pending
                $pendingOrder = Order::where('user_id', $user->id)
                    ->where('status', 'pending')
                    ->latest()
                    ->first();

                // 2. Jika ada, batalkan order tersebut
                if ($pendingOrder) {
                    $pendingOrder->update(['status' => 'cancelled']);
                }

                // 3. Ambil detail harga dari paket langganan user
                $userPackage = $user->userPackage;
                if (!$userPackage) {
                    // Ini adalah kemungkinan sumber error. Lemparkan exception jika paket tidak ada.
                    throw new Exception('User package not found for user ID: ' . $user->id);
                }

                // KALKULASI HARGA DENGAN DISKON TAHUNAN 
                $subscriptionPackage = $userPackage->subscriptionPackage;
                $price = 0;

                if ($userPackage->plan_type === 'yearly') {
                    $basePrice = $subscriptionPackage->yearly_price;
                    $discountPercentage = $subscriptionPackage->yearly_discount ?? 0;
                    $discountAmount = ($basePrice * $discountPercentage) / 100;
                    $price = $basePrice - $discountAmount;
                } else { // Jika 'monthly' atau tipe lainnya
                    $price = $subscriptionPackage->monthly_price;
                }

                // 4. Buat Order BARU dengan ID yang BARU dan UNIK
                $newOrder = Order::create([
                    'user_id' => $user->id,
                    'status' => 'pending',
                    'order_date' => now(),
                    'total_price' => $price,
                ]);
            });

            if (!$newOrder) {
                throw new Exception('Failed to create a new order.');
            }

            // Setup Midtrans...
            Config::$serverKey = config('services.midtrans.server_key');
            Config::$isProduction = config('services.midtrans.is_production');
            Config::$isSanitized = true;
            Config::$is3ds = true;

            $params = [
                'transaction_details' => [
                    'order_id' => $newOrder->id,
                    'gross_amount' => $newOrder->total_price,
                ],
                'customer_details' => [
                    'first_name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                ],
            ];

            $snapToken = Snap::getSnapToken($params);
            return response()->json(['snap_token' => $snapToken]);

        } catch (Exception $e) {
            // Jika ada error apa pun di dalam blok try, catat dan kirim respons JSON
            Log::error("Failed to generate Snap Token: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            // Kirim respons JSON yang valid agar tidak merusak front-end
            return response()->json(['error' => 'Terjadi kesalahan di server. Silakan coba lagi nanti.'], 500);
        }
    }
    public function applyVoucher(Request $request)
    { /* ... kode Anda yang sudah ada ... */
    }


    /**
     * handleWebhook yang dimodifikasi HANYA untuk tes dengan Postman.
     * Metode ini melewati validasi signature key.
     * * PENTING: Kembalikan ke kode semula setelah selesai melakukan tes!
     */
    public function handleWebhook(Request $request, MidtransWebhookService $webhookService)
    {
        Log::info('Midtrans Webhook (POSTMAN TEST): Request diterima.', $request->all());

        try {
            // 1. Ambil semua data dari request Postman
            $notificationPayload = (object) $request->all();

            // 2. Periksa apakah data penting ada
            if (empty($notificationPayload->order_id) || empty($notificationPayload->transaction_status)) {
                throw new Exception('Payload notifikasi tidak valid dari Postman.');
            }

            // 3. Tambahkan method getResponse() agar kompatibel dengan service Anda
            // Ini adalah trik agar kita tidak perlu mengubah MidtransWebhookService
            $notificationPayload->getResponse = function () use ($notificationPayload) {
                return json_encode($notificationPayload);
            };

            // 4. Panggil service dengan data "palsu" yang kita buat
            $webhookService->handle($notificationPayload);

            // 5. Beri respons OK
            return response()->json(['status' => 'ok', 'message' => 'Webhook processed successfully from Postman.']);

        } catch (Exception $e) {
            Log::error('Midtrans Webhook Error (POSTMAN TEST): ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['status' => 'error', 'message' => 'Webhook Error: ' . $e->getMessage()], 400);
        }
    }
}
