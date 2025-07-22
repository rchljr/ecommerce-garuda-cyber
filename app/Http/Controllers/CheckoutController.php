<?php

namespace App\Http\Controllers;

use Exception;
use Throwable;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Voucher;
use App\Models\Shipping;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\CartService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

class CheckoutController extends Controller
{
    // ... (constructor dan metode lain seperti index, getDetails tetap sama)
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
        \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
        \Midtrans\Config::$isProduction = config('services.midtrans.is_production');
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;
    }

    public function index(Request $request)
    {
        $validated = $request->validate(['items' => 'required|array|min:1', 'items.*' => 'string']);
        $itemIds = $validated['items'];
        $checkoutItems = $this->cartService->getItemsByIds($itemIds);
        if ($checkoutItems->isEmpty()) {
            return redirect()->route('tenant.cart.index', ['subdomain' => request()->route('subdomain')])->with('error', 'Produk yang dipilih tidak ditemukan.');
        }
        return view('customer.checkout', ['checkoutItemIds' => $itemIds]);
    }

    public function getDetails(Request $request)
    {
        $validated = $request->validate(['items' => 'required|array', 'items.*' => 'string']);
        $checkoutItems = $this->cartService->getItemsByIds($validated['items']);

        if ($checkoutItems->isEmpty()) {
            return response()->json(['error' => 'Item tidak ditemukan.'], 404);
        }

        $groupedItems = $checkoutItems->groupBy('product.shopOwner.shop.id');
        $shopsData = [];
        $grandSubtotal = 0;

        foreach ($groupedItems as $shopId => $items) {
            $firstItem = $items->first();
            if (!$firstItem || !$firstItem->product || !$firstItem->product->shopOwner || !$firstItem->product->shopOwner->shop)
                continue;

            $shopOwner = $firstItem->product->shopOwner;
            $shop = $shopOwner->shop;
            $shopSubtotal = $items->sum(fn($item) => $item->variant->price * $item->quantity);
            $grandSubtotal += $shopSubtotal;

            $vouchers = Voucher::where('user_id', $shopOwner->id)
                ->where('expired_date', '>=', now())
                ->latest('created_at')->get()
                ->map(function ($voucher) use ($shopSubtotal) {
                    $voucher->is_eligible = $shopSubtotal >= $voucher->min_spending;
                    return $voucher;
                });

            $shopsData[] = [
                'shop' => ['id' => $shop->id, 'shop_name' => $shop->shop_name, 'postal_code' => $shop->postal_code],
                'items' => $items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'product_name' => $item->product->name,
                        'main_image' => $item->variant->image_path ?? $item->product->main_image,
                        'variant_name' => $item->variant->name,
                        'quantity' => $item->quantity,
                        'price' => $item->variant->price,
                        'total_price' => $item->quantity * $item->variant->price,
                    ];
                })->values(),
                'subtotal' => $shopSubtotal,
                'origin_postal_code' => $shop->postal_code ?? '28293',
                'vouchers' => $vouchers,
                'item_ids_for_shipping' => $items->pluck('id'),
            ];
        }

        $customer = Auth::guard('customers')->user();

        return response()->json([
            'shopsData' => $shopsData,
            'grandSubtotal' => $grandSubtotal,
            'customer' => $customer ? ['alamat' => $customer->alamat] : ['alamat' => ''],
        ]);
    }

    public function charge(Request $request)
    {
        // PERBAIKAN: Tambahkan validasi untuk kota dan kode pos
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*' => 'string',
            'delivery_method' => 'required|string|in:ship,pickup',
            'payment_method' => 'required|string',
            'alamat' => 'required_if:delivery_method,ship|nullable|string|max:500',
            'shipping_city' => 'required_if:delivery_method,ship|nullable|string',
            'shipping_zip_code' => 'required_if:delivery_method,ship|nullable|string',
            'shops' => 'present|array',
            'shops.*.shippingCost' => 'required_if:delivery_method,ship|numeric',
            'shops.*.shippingService' => 'required_if:delivery_method,ship|nullable|string',
            'shops.*.voucherId' => 'nullable|string',
            'shops.*.notes' => 'nullable|string|max:500',
        ]);

        $orderGroupId = Str::uuid();

        try {
            $customer = Auth::guard('customers')->user();
            if (!$customer) {
                return response()->json(['error' => 'Anda harus login untuk melanjutkan.'], 401);
            }

            $checkoutItems = $this->cartService->getItemsByIds($validated['items']);
            if ($checkoutItems->isEmpty()) {
                return response()->json(['error' => 'Item tidak ditemukan.'], 404);
            }

            $totalSubtotal = 0;
            $totalShipping = 0;
            $totalDiscount = 0;
            $groupedItems = $checkoutItems->groupBy('product.shopOwner.shop.id');

            foreach ($groupedItems as $shopId => $items) {
                $shopDataFromRequest = $validated['shops'][$shopId] ?? null;
                $shopSubtotal = $items->sum(fn($item) => $item->variant->price * $item->quantity);
                $shopShippingCost = ($validated['delivery_method'] === 'ship' && $shopDataFromRequest) ? ($shopDataFromRequest['shippingCost'] ?? 0) : 0;
                $shopDiscount = 0;

                if ($shopDataFromRequest && !empty($shopDataFromRequest['voucherId'])) {
                    $voucher = Voucher::find($shopDataFromRequest['voucherId']);
                    if ($voucher && $voucher->user_id == $items->first()->product->user_id && $shopSubtotal >= $voucher->min_spending) {
                        $shopDiscount = ($shopSubtotal * $voucher->discount) / 100;
                    }
                }
                $totalSubtotal += $shopSubtotal;
                $totalShipping += $shopShippingCost;
                $totalDiscount += $shopDiscount;
            }

            $grandTotalForMidtrans = round($totalSubtotal - $totalDiscount + $totalShipping);
            if ($grandTotalForMidtrans <= 0)
                $grandTotalForMidtrans = 1;

            $itemDetailsForMidtrans = [];
            if ($totalSubtotal > 0)
                $itemDetailsForMidtrans[] = ['id' => 'SUBTOTAL', 'price' => round($totalSubtotal), 'quantity' => 1, 'name' => 'Total Belanja Produk'];
            if ($totalShipping > 0)
                $itemDetailsForMidtrans[] = ['id' => 'SHIPPING', 'price' => round($totalShipping), 'quantity' => 1, 'name' => 'Total Ongkos Kirim'];
            if ($totalDiscount > 0)
                $itemDetailsForMidtrans[] = ['id' => 'DISCOUNT', 'price' => -round($totalDiscount), 'quantity' => 1, 'name' => 'Total Diskon'];

            $paymentTransactionId = 'PAY-' . $orderGroupId;
            $params = [
                'transaction_details' => ['order_id' => $paymentTransactionId, 'gross_amount' => $grandTotalForMidtrans],
                'customer_details' => ['first_name' => $customer->name, 'email' => $customer->email, 'phone' => $customer->phone],
                'item_details' => $itemDetailsForMidtrans
            ];

            $payment_type = $validated['payment_method'];
            if ($payment_type === 'va_bca' || $payment_type === 'va_bri') {
                $params['payment_type'] = 'bank_transfer';
                $params['bank_transfer'] = ['bank' => str_replace('va_', '', $payment_type)];
            } else {
                $params['payment_type'] = $payment_type;
            }

            DB::beginTransaction();

            foreach ($groupedItems as $shopId => $items) {
                $shopDataFromRequest = $validated['shops'][$shopId] ?? null;
                $firstItem = $items->first();
                $shopSubtotal = $items->sum(fn($item) => $item->variant->price * $item->quantity);
                $shopShippingCost = ($validated['delivery_method'] === 'ship' && $shopDataFromRequest) ? ($shopDataFromRequest['shippingCost'] ?? 0) : 0;
                $shopDiscount = 0;
                $voucherId = null;

                if ($shopDataFromRequest && !empty($shopDataFromRequest['voucherId'])) {
                    $voucher = Voucher::find($shopDataFromRequest['voucherId']);
                    if ($voucher && $voucher->user_id == $items->first()->product->user_id && $shopSubtotal >= $voucher->min_spending) {
                        $shopDiscount = ($shopSubtotal * $voucher->discount) / 100;
                        $voucherId = $voucher->id;
                    }
                }

                $order = Order::create([
                    'order_group_id' => $orderGroupId,
                    'user_id' => $customer->id,
                    'shop_id' => $shopId,
                    'subdomain_id' => $firstItem->product->shopOwner->subdomain->id,
                    'voucher_id' => $voucherId,
                    'total_price' => ($shopSubtotal - $shopDiscount) + $shopShippingCost,
                    'subtotal' => $shopSubtotal,
                    'shipping_cost' => $shopShippingCost,
                    'discount_amount' => $shopDiscount,
                    'order_date' => now(),
                    'status' => 'pending',
                    'delivery_method' => $validated['delivery_method'],
                    'notes' => $shopDataFromRequest['notes'] ?? null,
                    'shipping_address' => $validated['alamat'] ?? null,
                    'shipping_city' => $validated['shipping_city'] ?? null,
                    'shipping_zip_code' => $validated['shipping_zip_code'] ?? null,
                    'shipping_phone' => $customer->phone,
                ]);

                foreach ($items as $item) {
                    $order->items()->create([
                        'product_id' => $item->product_id,
                        'product_variant_id' => $item->product_variant_id,
                        'quantity' => $item->quantity,
                        'price' => $item->variant->price
                    ]);
                }

                if ($validated['delivery_method'] === 'ship') {
                    Shipping::create([
                        'order_id' => $order->id,
                        'delivery_service' => $shopDataFromRequest['shippingService'] ?? 'N/A',
                        'status' => 'pending', // Status awal pengiriman
                        'shipping_cost' => $shopShippingCost
                    ]);
                }
            }

            $response = \Midtrans\CoreApi::charge($params);

            Payment::create([
                'order_group_id' => $orderGroupId,
                'user_id' => $customer->id,
                'midtrans_order_id' => $response->order_id,
                'midtrans_transaction_status' => $response->transaction_status,
                'midtrans_payment_type' => $response->payment_type,
                'total_payment' => $response->gross_amount,
                'midtrans_response' => json_encode($response)
            ]);

            $this->cartService->clearCartItems($validated['items']);

            DB::commit();
            return response()->json($response);

        } catch (Throwable $e) {
            if (DB::transactionLevel() > 0)
                DB::rollBack();
            Log::error('Checkout Charge Failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Terjadi kesalahan pada server saat memproses pembayaran.'], 500);
        }
    }

    public function searchDestination(Request $request)
    {
        $keyword = $request->input('keyword');
        if (!$keyword)  
            return response()->json(['areas' => []]);
        try {
            $apiKey = Config::get('biteship.api_key');
            $baseUrl = Config::get('biteship.base_url');
            if (empty($apiKey))
                return response()->json(['error' => 'Konfigurasi API pengiriman tidak ditemukan.'], 500);
            $response = Http::withToken($apiKey)->get($baseUrl . '/v1/maps/areas', ['countries' => 'ID', 'input' => $keyword, 'type' => 'subdistrict']);
            $responseData = $response->json();
            if ($response->successful() && !empty($responseData['areas']))
                return response()->json($responseData['areas']);
            return response()->json(['error' => 'Lokasi tidak ditemukan.'], 404);
        } catch (Exception $e) {
            Log::error('Biteship Area Search Exception: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan pada server pencarian.'], 500);
        }
    }

    public function calculateShipping(Request $request)
    {
        $validated = $request->validate(['origin_postal_code' => 'required|string', 'destination_postal_code' => 'required|string', 'items' => 'required|array']);
        try {
            $apiKey = Config::get('biteship.api_key');
            $baseUrl = Config::get('biteship.base_url');
            $cartItems = $this->cartService->getItemsByIds($validated['items']);
            $itemsPayload = $cartItems->map(fn($item) => [
                'name' => $item->product->name,
                'description' => 'Varian',
                'value' => $item->variant->price,
                'length' => $item->product->length ?? 10,
                'width' => $item->product->width ?? 10,
                'height' => $item->product->height ?? 10,
                'weight' => $item->product->weight ?? 500,
                'quantity' => $item->quantity,
            ])->toArray();
            $response = Http::withToken($apiKey)->post($baseUrl . '/v1/rates/couriers', [
                'origin_postal_code' => $validated['origin_postal_code'],
                'destination_postal_code' => $validated['destination_postal_code'],
                'couriers' => 'jne,jnt,sicepat,anteraja,gojek,grab',
                'items' => $itemsPayload,
            ]);
            $responseData = $response->json();
            if ($response->successful() && !empty($responseData['pricing']))
                return response()->json($responseData['pricing']);
            return response()->json(['error' => $responseData['error'] ?? 'Gagal menghitung ongkir.'], $response->status());
        } catch (Exception $e) {
            Log::error('Biteship Rates Exception: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan pada server kalkulasi.'], 500);
        }
    }
}