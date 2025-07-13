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
        $originPostalCode = $shop->postal_code ?? '28293';

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
            'originPostalCode',
            'totalWeightInKg',
            'shop',
        ));
    }

    /**
     * PERBAIKAN: Kembali ke fungsi pencarian berdasarkan keyword.
     */
    public function searchDestination(Request $request)
    {
        $keyword = $request->input('keyword');
        if (!$keyword) {
            return response()->json(['areas' => []]);
        }
        try {
            $apiKey = Config::get('biteship.api_key');
            $baseUrl = Config::get('biteship.base_url');

            if (empty($apiKey)) {
                Log::error('Biteship API Key is not configured.');
                return response()->json(['error' => 'Konfigurasi API pengiriman tidak ditemukan.'], 500);
            }

            $response = Http::withToken($apiKey)
                ->get($baseUrl . '/v1/maps/areas', [
                    'countries' => 'ID',
                    'input' => $keyword,
                    'type' => 'subdistrict'
                ]);

            $responseData = $response->json();
            Log::info('Biteship Area Search Response:', ['status' => $response->status(), 'body' => $responseData]);

            if ($response->successful() && !empty($responseData['areas'])) {
                return response()->json($responseData['areas']);
            }

            return response()->json(['error' => 'Lokasi tidak ditemukan.'], 404);
        } catch (Exception $e) {
            Log::error('Biteship Area Search Exception: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan pada server pencarian.'], 500);
        }
    }

    public function calculateShipping(Request $request)
    {
        $validated = $request->validate([
            'origin_postal_code' => 'required|string',
            'destination_postal_code' => 'required|string',
            'items' => 'required|array'
        ]);

        try {
            $apiKey = Config::get('biteship.api_key');
            $baseUrl = Config::get('biteship.base_url');

            $cartItems = $this->cartService->getItemsByIds($validated['items']);
            $itemsPayload = $cartItems->map(function ($item) {
                return [
                    'name' => $item->product->name,
                    'description' => 'Varian ' . optional($item->variant)->size . ' / ' . optional($item->variant)->color,
                    'value' => $item->product->price,
                    'length' => $item->product->length ?? 10,
                    'width' => $item->product->width ?? 10,
                    'height' => $item->product->height ?? 10,
                    'weight' => $item->product->weight ?? 500,
                    'quantity' => $item->quantity,
                ];
            })->toArray();

            $response = Http::withToken($apiKey)
                ->post($baseUrl . '/v1/rates/couriers', [
                    'origin_postal_code' => $validated['origin_postal_code'],
                    'destination_postal_code' => $validated['destination_postal_code'],
                    'couriers' => 'jne,jnt,sicepat,anteraja,gojek,grab',
                    'items' => $itemsPayload,
                ]);

            $responseData = $response->json();

            if ($response->successful() && !empty($responseData['pricing'])) {
                return response()->json($responseData['pricing']);
            }

            Log::error('Biteship Rates Error', ['response' => $responseData]);
            return response()->json(['error' => $responseData['error'] ?? 'Gagal menghitung ongkir.'], $response->status());

        } catch (Exception $e) {
            Log::error('Biteship Rates Exception: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan pada server kalkulasi.'], 500);
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
            'estimated_delivery' => 'nullable|string',
            'alamat' => 'required_if:delivery_method,ship|nullable|string|max:500',
            'payment_method' => 'required|string|in:bca_va,bni_va,gopay,qris',
            'voucher_id' => 'nullable|exists:vouchers,id',
        ]);

        $customer = Auth::guard('customers')->user();
        $checkoutItems = $this->cartService->getItemsByIds($validated['items']);

        if ($checkoutItems->isEmpty()) {
            return response()->json(['error' => 'Item tidak ditemukan.'], 404);
        }

        $shopOwner = $checkoutItems->first()->product->shopOwner;
        $subdomainId = $shopOwner->subdomain->id;

        $subtotal = $checkoutItems->sum(fn($item) => $item->product->price * $item->quantity);
        $shippingCost = ($validated['delivery_method'] === 'ship') ? ($validated['shipping_cost'] ?? 0) : 0;
        $discountAmount = 0;
        $voucher = null;

        // 1. Validasi dan hitung ulang diskon di server
        if (!empty($validated['voucher_id'])) {
            $voucher = Voucher::find($validated['voucher_id']);
            // Pastikan voucher valid, milik toko ini, aktif, dan subtotal memenuhi syarat
            if ($voucher && $voucher->user_id === $shopOwner->id && $voucher->expired_date >= now() && $subtotal >= $voucher->min_spending) {
                $discountAmount = ($subtotal * $voucher->discount) / 100;
            } else {
                // Jika voucher tidak valid, batalkan penggunaan
                $validated['voucher_id'] = null;
            }
        }

        // 2. Hitung total harga akhir yang akan dibayar
        $finalPrice = ($subtotal - $discountAmount) + $shippingCost;
        $finalPrice = max(0, round($finalPrice)); // Pastikan tidak negatif dan bulatkan

        DB::beginTransaction();
        try {
            // Gunakan updateOrCreate untuk menghindari duplikasi order jika user refresh halaman
            $order = Order::updateOrCreate(
                ['user_id' => $customer->id, 'subdomain_id' => $subdomainId, 'status' => 'pending'],
                [
                    'total_price' => $finalPrice,
                    'subtotal' => $subtotal,
                    'shipping_cost' => $shippingCost,
                    'discount_amount' => $discountAmount,
                    'voucher_id' => $validated['voucher_id'],
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

            if ($validated['delivery_method'] === 'ship') {
                $shippingAddress = !empty(trim($validated['alamat'])) ? $validated['alamat'] : $customer->alamat;

                Shipping::updateOrCreate(
                    ['order_id' => $order->id],
                    [
                        'delivery_service' => $validated['shipping_service'],
                        'shipping_cost' => $shippingCost,
                        'status' => 'pending',
                        'shipping_address' => $shippingAddress,
                        'estimated_delivery' => $validated['estimated_delivery'] ?? null,
                    ]
                );
            }

            $transactionId = $order->id . '-' . uniqid();

            // 3. Siapkan item_details untuk Midtrans
            $itemDetails = $checkoutItems->map(function ($item) {
                return [
                    'id' => $item->product_variant_id,
                    'price' => $item->product->price,
                    'quantity' => $item->quantity,
                    'name' => substr($item->product->name, 0, 50),
                ];
            });

            if ($shippingCost > 0) {
                $itemDetails->push(['id' => 'SHIPPING', 'price' => $shippingCost, 'quantity' => 1, 'name' => 'Ongkos Kirim']);
            }

            if ($discountAmount > 0) {
                $itemDetails->push(['id' => 'VOUCHER_' . ($voucher->code ?? 'DISCOUNT'), 'price' => -round($discountAmount), 'quantity' => 1, 'name' => 'Diskon Voucher']);
            }

            $params = [
                'transaction_details' => [
                    'order_id' => $transactionId,
                    'gross_amount' => $finalPrice, // Kirim harga final ke Midtrans
                ],
                'customer_details' => ['first_name' => $customer->name, 'email' => $customer->email, 'phone' => $customer->phone,],
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
