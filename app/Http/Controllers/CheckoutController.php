<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Shipping;
use App\Models\User;
use App\Models\Contact; // <-- TAMBAHKAN: Import model Contact
use Illuminate\Http\Request;
use App\Services\CartService;
use App\Services\VoucherService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Midtrans\Snap;

class CheckoutController extends Controller
{
    protected $cartService;
    protected $voucherService;

    public function __construct(CartService $cartService, VoucherService $voucherService)
    {
        $this->cartService = $cartService;
        $this->voucherService = $voucherService;
        // Setup Midtrans Config
        \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
        \Midtrans\Config::$isProduction = config('services.midtrans.is_production');
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;
    }

    /**
     * Menampilkan halaman checkout dengan data yang diperlukan.
     */
    public function index(Request $request)
    {
        $validated = $request->validate(['items' => 'required|array|min:1', 'items.*' => 'string']);
        $itemIds = $validated['items'];
        $checkoutItems = $this->cartService->getItemsByIds($itemIds);

        if ($checkoutItems->isEmpty()) {
            return redirect()->route('tenant.cart.index', ['subdomain' => $request->route('subdomain')])->with('error', 'Produk yang dipilih tidak ditemukan.');
        }

        $subtotal = $checkoutItems->sum(fn($item) => $item->product->price * $item->quantity);
        $totalWeightInKg = $checkoutItems->sum(fn($item) => ($item->product->weight ?? 100) * $item->quantity) / 1000; // Konversi ke KG

        $customer = Auth::guard('customers')->user();
        $firstItem = $checkoutItems->first();

        // Pastikan relasi ini ada dan benar untuk mendapatkan data mitra
        $shopOwner = $firstItem->product->shopOwner;
        $shop = $shopOwner->shop;

        // --- Mengambil data kontak dari relasi ---
        // Asumsi: Model User (ShopOwner) memiliki relasi hasOne ke Contact
        // Jika tidak, gunakan: $contact = Contact::where('user_id', $shopOwner->id)->first();
        $contact = $shopOwner->contact;

        // Asumsi mitra punya destination_id yang didapat dari API Komerce
        $originId = $shop->komerce_destination_id;

        // Filter voucher berdasarkan subtotal DAN pemilik toko (mitra)
        $vouchers = $this->voucherService->getApplicableVouchers($subtotal, (int) $shopOwner->id);

        // Kirim semua data yang diperlukan ke view
        return view('customer.checkout', compact(
            'checkoutItems',
            'subtotal',
            'customer',
            'vouchers',
            'originId',
            'totalWeightInKg',
            'shop',      // Untuk nama toko
            'contact'    // <-- TAMBAHKAN: Untuk alamat, telepon, jam kerja
        ));
    }

    /**
     * --- BARU: Mencari tujuan pengiriman berdasarkan keyword ---
     */
    public function searchDestination(Request $request)
    {
        // CATATAN: Kode ini sementara akan gagal sampai masalah API Key terselesaikan.
        $keyword = $request->query('keyword');
        if (!$keyword) {
            return response()->json(['data' => []]);
        }
        try {
            $apiKey = Config::get('rajaongkir.api_key');
            $baseUrl = Config::get('rajaongkir.base_url');

            $response = Http::withHeaders(['key' => $apiKey])
                ->get($baseUrl . '/tariff/api/v1/destination/search', ['keyword' => $keyword]);

            Log::info('Komerce API Response: ' . $response->body());

            $responseData = $response->json();

            if ($response->successful() && isset($responseData['data']) && is_array($responseData['data'])) {
                return response()->json($responseData['data']);
            }

            Log::error('Gagal mencari lokasi atau respons API tidak valid.', ['response' => $response->body()]);
            return response()->json(['error' => 'Gagal mencari lokasi.'], 502);

        } catch (\Exception $e) {
            Log::error('Komerce Search Destination Exception: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan pada server.'], 500);
        }
    }

    /**
     * --- BARU: Menghitung ongkos kirim berdasarkan ID asal dan tujuan ---
     */
    public function calculateShipping(Request $request)
    {
        $validated = $request->validate([
            'origin_id' => 'required|integer',
            'destination_id' => 'required|integer',
            'weight' => 'required|numeric',
        ]);
        try {
            $apiKey = Config::get('rajaongkir.api_key');
            $baseUrl = Config::get('rajaongkir.base_url');

            $response = Http::withHeaders(['key' => $apiKey])
                ->get($baseUrl . '/tariff/api/v1/calculate', [
                    'origin_region_id' => $validated['origin_id'],
                    'destination_region_id' => $validated['destination_id'],
                    'weight' => $validated['weight'],
                ]);

            if ($response->successful() && isset($response->json()['data'])) {
                return response()->json($response->json()['data']);
            }
            return response()->json(['error' => 'Gagal menghitung ongkir.'], 502);
        } catch (\Exception $e) {
            Log::error('Komerce Calculate Shipping Error: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan pada server.'], 500);
        }
    }

    /**
     * Memproses pesanan dan memulai sesi pembayaran Midtrans.
     */
    public function process(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'delivery_method' => 'required|string|in:ship,pickup',
            'shipping_cost' => 'nullable|numeric',
            'shipping_service' => 'nullable|string',
            'voucher_id' => 'nullable|exists:vouchers,id',
            'discount_amount' => 'nullable|numeric',
            'alamat' => 'required_if:delivery_method,ship|nullable|string|max:500',
        ]);

        $customer = Auth::guard('customers')->user();
        $checkoutItems = $this->cartService->getItemsByIds($validated['items']);

        if ($checkoutItems->isEmpty()) {
            return response()->json(['error' => 'Item tidak ditemukan.'], 404);
        }

        $firstItem = $checkoutItems->first();
        if (!$firstItem?->product?->shopOwner?->subdomain) {
            return response()->json(['error' => 'Informasi toko tidak lengkap.'], 500);
        }
        $subdomainId = $firstItem->product->shopOwner->subdomain->id;

        $subtotal = $checkoutItems->sum(fn($item) => $item->product->price * $item->quantity);
        $shippingCost = ($validated['delivery_method'] === 'ship') ? ($validated['shipping_cost'] ?? 0) : 0;
        $discountAmount = $validated['discount_amount'] ?? 0;
        $totalPrice = $subtotal + $shippingCost - $discountAmount;

        $order = null;
        DB::beginTransaction();
        try {
            $order = Order::create([
                'user_id' => $customer->id,
                'subdomain_id' => $subdomainId,
                'voucher_id' => $validated['voucher_id'] ?? null,
                'status' => 'pending',
                'order_date' => now(),
                'total_price' => $totalPrice,
            ]);

            foreach ($checkoutItems as $item) {
                $order->items()->create(['product_id' => $item->product->id, 'quantity' => $item->quantity, 'unit_price' => $item->product->price]);
            }

            if ($validated['delivery_method'] === 'ship') {
                Shipping::create(['order_id' => $order->id, 'delivery_service' => $validated['shipping_service'], 'status' => 'pending', 'shipping_cost' => $shippingCost]);
            }

            Payment::create(['order_id' => $order->id, 'user_id' => $customer->id, 'midtrans_payment_type' => 'midtrans', 'total_payment' => $totalPrice, 'midtrans_transaction_status' => 'pending']);
            $order->histories()->create(['user_id' => $customer->id]);

            if ($request->filled('alamat') && $request->alamat !== $customer->alamat) {
                $customer->update(['alamat' => $request->alamat]);
            }

            $this->cartService->clearCartItems($validated['items']);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order Creation Failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Gagal membuat pesanan. Silakan coba lagi.'], 500);
        }

        $params = [
            'transaction_details' => ['order_id' => $order->id, 'gross_amount' => $order->total_price],
            'customer_details' => ['first_name' => $customer->name, 'email' => $customer->email, 'phone' => $customer->phone],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            if ($order->payment) {
                $order->payment->update(['snap_token' => $snapToken]);
            }
            return response()->json(['snapToken' => $snapToken]);
        } catch (\Exception $e) {
            Log::error('Midtrans Snap Token Failed: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal memulai sesi pembayaran.'], 500);
        }
    }
}
