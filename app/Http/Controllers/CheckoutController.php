<?php

namespace App\Http\Controllers;

use Exception;
use Midtrans\Snap;
use App\Models\User;
use App\Models\Order;
use Midtrans\CoreApi;
use App\Models\Contact;
use App\Models\Payment;
use App\Models\Voucher;
use App\Models\Shipping;
use Illuminate\Http\Request;
use App\Services\CartService;
use App\Services\VoucherService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Client\ConnectionException;

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
        $shopOwner = $firstItem->product->shopOwner;
        $shop = $shopOwner->shop;
        $contact = $shopOwner->contact;
        $originId = $shop->komerce_destination_id;
        // Mengambil voucher yang sesuai dengan dua kriteria:
        // 1. Dimiliki oleh toko yang sedang dikunjungi (user_id cocok).
        // 2. Masih aktif (tanggal kedaluwarsa belum lewat).
        $vouchers = Voucher::where('user_id', $shopOwner->id)
            ->where('expired_date', '>=', now())
            ->latest('created_at')
            ->get();

        return view('customer.checkout', compact(
            'checkoutItems',
            'subtotal',
            'customer',
            'vouchers',
            'originId',
            'totalWeightInKg',
            'shop',
            'contact'
        ));
    }

    /**
     * Mencari tujuan pengiriman berdasarkan keyword menggunakan metode POST.
     */
    public function searchDestination(Request $request)
    {
        $validated = $request->validate(['keyword' => 'required|string|min:3']);
        $keyword = $validated['keyword'];

        try {
            $apiKey = Config::get('rajaongkir.api_key');
            $baseUrl = Config::get('rajaongkir.base_url');

            $response = Http::withHeaders(['x-api-key' => $apiKey])
                ->post($baseUrl . '/api/v1/destination/search', [
                    'keyword' => $keyword
                ]);

            Log::info('Komerce API Search Response (POST): ' . $response->body());
            $responseData = $response->json();

            if ($response->successful() && isset($responseData['data']) && is_array($responseData['data'])) {
                return response()->json($responseData['data']);
            }

            Log::error('Gagal mencari lokasi.', ['response' => $response->body()]);
            return response()->json(['error' => 'Gagal mencari lokasi dari API.'], $response->status());

        } catch (Exception $e) {
            Log::error('Komerce Search Exception: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan pada server.'], 500);
        }
    }

    /**
     * Menghitung ongkos kirim berdasarkan ID asal dan tujuan menggunakan metode POST.
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

            $response = Http::withHeaders(['x-api-key' => $apiKey])
                ->post($baseUrl . '/api/v1/calculate', [
                    'shipper_destination_id' => $validated['origin_id'],
                    'receiver_destination_id' => $validated['destination_id'],
                    'weight' => $validated['weight'],
                ]);

            Log::info('Komerce API Calculate Response (POST): ' . $response->body());
            $responseData = $response->json();

            if ($response->successful() && isset($responseData['data'])) {
                return response()->json($responseData['data']);
            }
            return response()->json(['error' => 'Gagal menghitung ongkir dari API.'], $response->status());
        } catch (Exception $e) {
            Log::error('Komerce Calculate Exception: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan pada server.'], 500);
        }
    }
    /**
     * Memproses pembayaran dengan Midtrans.
     * Metode ini membuat atau memperbarui Order dan Payment, lalu memanggil Midtrans.
     */
    public function charge(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'delivery_method' => 'required|string|in:ship,pickup',
            'shipping_cost' => 'nullable|numeric',
            'shipping_service' => 'nullable|string',
            'alamat' => 'required_if:delivery_method,ship|nullable|string|max:500',
            'payment_method' => 'required|string|in:bca_va,bni_va,gopay,qris',
            // Tambahkan validasi lain jika perlu (voucher, dll)
        ]);

        $customer = Auth::guard('customers')->user();
        $checkoutItems = $this->cartService->getItemsByIds($validated['items']);

        if ($checkoutItems->isEmpty()) {
            return response()->json(['error' => 'Item tidak ditemukan.'], 404);
        }

        $subdomainId = $checkoutItems->first()->product->shopOwner->subdomain->id;
        $subtotal = $checkoutItems->sum(fn($item) => $item->product->price * $item->quantity);
        $shippingCost = ($validated['delivery_method'] === 'ship') ? ($validated['shipping_cost'] ?? 0) : 0;
        $totalPrice = $subtotal + $shippingCost; // Kurangi diskon jika ada

        DB::beginTransaction();
        try {
            // Gunakan updateOrCreate untuk menghindari duplikasi order jika user refresh halaman
            $order = Order::updateOrCreate(
                [
                    'user_id' => $customer->id,
                    'subdomain_id' => $subdomainId,
                    'status' => 'pending',
                ],
                [
                    'total_price' => $totalPrice,
                    'order_date' => now(),
                ]
            );

            // Sinkronisasi item order
            $order->items()->delete(); // Hapus item lama untuk memastikan data baru
            foreach ($checkoutItems as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->product->price,
                ]);
            }

            // Buat atau update data pengiriman
            if ($validated['delivery_method'] === 'ship') {
                Shipping::updateOrCreate(
                    ['order_id' => $order->id],
                    [
                        'delivery_service' => $validated['shipping_service'],
                        'shipping_cost' => $shippingCost,
                        'status' => 'pending',
                        'shipping_address' => $validated['alamat'],
                    ]
                );
            }

            $transactionId = $order->id . '-' . uniqid();

            // Siapkan parameter untuk Midtrans Core API
            $itemDetails = $checkoutItems->map(function ($item) {
                return [
                    'id' => $item->product_variant_id,
                    'price' => $item->product->price,
                    'quantity' => $item->quantity,
                    'name' => substr($item->product->name, 0, 50),
                ];
            });

            if ($shippingCost > 0) {
                $itemDetails->push([
                    'id' => 'SHIPPING',
                    'price' => $shippingCost,
                    'quantity' => 1,
                    'name' => 'Ongkos Kirim',
                ]);
            }

            $params = [
                'transaction_details' => [
                    'order_id' => $transactionId,
                    'gross_amount' => $totalPrice,
                ],
                'customer_details' => [
                    'first_name' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                ],
                'item_details' => $itemDetails->toArray(),
            ];

            // Set payment_type berdasarkan input
            switch ($request->payment_method) {
                case 'bca_va':
                case 'bni_va':
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

            $response = CoreApi::charge($params);

            // Buat atau update catatan pembayaran
            Payment::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'user_id' => $customer->id,
                    'midtrans_order_id' => $response->order_id,
                    'midtrans_transaction_status' => $response->transaction_status,
                    'midtrans_payment_type' => $response->payment_type,
                    'total_payment' => $response->gross_amount,
                    'midtrans_response' => json_encode($response),
                ]
            );

            $this->cartService->clearCartItems($validated['items']); // Kosongkan keranjang

            DB::commit();
            return response()->json($response);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Checkout Charge Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Gagal memproses pembayaran: ' . $e->getMessage()], 500);
        }
    }
}
