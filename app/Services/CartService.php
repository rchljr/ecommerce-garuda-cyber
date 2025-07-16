<?php

namespace App\Services;

use Exception;
use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductCart;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartService
{
    /**
     * Menambahkan produk ke keranjang (Session atau Database).
     */
    public function add(Request $request): void
    {
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity', 1);

        $variant = ProductVariant::where('product_id', $productId)
            ->where('size', $request->input('size'))
            ->where('color', $request->input('color'))
            ->firstOrFail();

        if (Auth::guard('customers')->check()) {
            $this->addToDatabase($productId, $variant->id, $quantity);
        } else {
            $this->addToSession($productId, $variant, $quantity);
        }
    }

    private function addToDatabase(string $productId, int $variantId, int $quantity): void
    {
        $cart = Cart::firstOrCreate(['user_id' => Auth::guard('customers')->id()]);
        $item = $cart->items()->where('product_variant_id', $variantId)->first();

        if ($item) {
            $item->increment('quantity', $quantity);
        } else {
            $cart->items()->create([
                'product_id' => $productId,
                'product_variant_id' => $variantId,
                'quantity' => $quantity,
            ]);
        }
    }

    private function addToSession(string $productId, ProductVariant $variant, int $quantity): void
    {
        $cart = Session::get('cart', []);
        $variantId = $variant->id;

        if (isset($cart[$variantId])) {
            $cart[$variantId]['quantity'] += $quantity;
        } else {
            $product = Product::find($productId);
            $cart[$variantId] = [
                "id" => $variantId,
                "product_id" => $product->id,
                "product_variant_id" => $variantId,
                "quantity" => $quantity,
                "name" => $product->name,
                "price" => $product->price,
                "image" => $product->main_image,
                "size" => $variant->size,
                "color" => $variant->color,
            ];
        }
        Session::put('cart', $cart);
    }

    /**
     * Menggabungkan keranjang dari session ke database setelah login.
     */
    public function mergeSessionCart(): void
    {
        if (!Session::has('cart') || empty(Session::get('cart'))) {
            return;
        }

        $userId = Auth::guard('customers')->id();
        $sessionCart = Session::get('cart');

        if (!$userId || !$sessionCart) {
            return;
        }

        Log::info("Merging cart for user {$userId}. Session data: ", $sessionCart);

        $userCart = Cart::firstOrCreate(['user_id' => $userId]);
        $dbItems = $userCart->items()->get()->keyBy('product_variant_id');

        DB::beginTransaction();
        try {
            foreach ($sessionCart as $variantId => $sessionItem) {
                if (!isset($sessionItem['product_id']) || !isset($sessionItem['quantity'])) {
                    continue;
                }

                if (isset($dbItems[$variantId])) {
                    $dbItems[$variantId]->increment('quantity', $sessionItem['quantity']);
                } else {
                    ProductCart::create([
                        'cart_id' => $userCart->id,
                        'product_id' => $sessionItem['product_id'],
                        'product_variant_id' => $variantId,
                        'quantity' => $sessionItem['quantity'],
                    ]);
                }
            }
            DB::commit();
            Log::info("Cart for user {$userId} merged successfully. Forgetting session cart.");
            Session::forget('cart');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Failed to merge cart for user {$userId}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Mengambil semua item dari keranjang dengan data produk dan varian terkait.
     */
    public function getItems(): Collection
    {
        if (Auth::guard('customers')->check()) {
            $cart = Cart::where('user_id', Auth::guard('customers')->id())->first();
            if (!$cart) {
                return collect();
            }

            // Menampilkan SEMUA item dari keranjang pengguna, tanpa filter toko.
            return $cart->items()
                ->with(['shop', 'subdomain']) // Eager load relasi untuk menampilkan info toko
                ->latest() // Mengurutkan berdasarkan 'created_at' secara descending
                ->get();
        }

        // --- Logika untuk Tamu (Guest) ---
        $sessionCart = Session::get('cart', []);
        if (empty($sessionCart)) {
            return collect();
        }

        $variantIds = array_keys($sessionCart);

        // Mengambil SEMUA varian dari session, tanpa filter toko.
        $variants = ProductVariant::with([
            'product.shopOwner' => function ($query) {
                $query->with(['subdomain', 'shop']);
            }
        ])
            ->whereIn('id', $variantIds)
            ->get()
            ->keyBy('id');

        return collect($sessionCart)->map(function ($item) use ($variants) {
            $variant = $variants->get($item['product_variant_id']);
            if (!$variant) {
                return null;
            }

            return (object) [
                'id' => $item['id'],
                'cart_id' => 'session',
                'product_id' => $item['product_id'],
                'product_variant_id' => $item['product_variant_id'],
                'quantity' => $item['quantity'],
                'product' => $variant->product,
                'variant' => $variant,
                'created_at' => $item['added_at'] ?? now()->toIso8601String(),
            ];
        })->filter()
            ->sortByDesc('created_at');
    }

    /**
     * Memperbarui kuantitas item.
     */
    public function update(string $itemId, int $quantity): bool
    {
        if (Auth::guard('customers')->check()) {
            $userId = Auth::guard('customers')->id();
            $item = ProductCart::with('cart')->find($itemId);

            if (!$item) {
                Log::warning("Cart update failed: ProductCart item with ID {$itemId} not found.");
                return false; // Gagal: Item tidak ditemukan
            }

            // Verifikasi bahwa item ini benar-benar milik user yang sedang login
            if ($item->cart && $item->cart->user_id === $userId) {
                $item->quantity = $quantity;
                $item->save(); // Simpan perubahan
                Log::info("Successfully updated quantity for ProductCart ID: {$itemId}");
                return true; // Sukses
            } else {
                Log::warning("Cart update authorization failed for ProductCart ID: {$itemId}. User {$userId} does not own this item or item has no cart.");
                return false; // Gagal: Otorisasi gagal
            }
        } else {
            // Logika untuk tamu (session)
            $cart = Session::get('cart', []);
            if (isset($cart[$itemId])) {
                $cart[$itemId]['quantity'] = $quantity;
                Session::put('cart', $cart);
                return true; // Sukses untuk session
            }
            return false; // Gagal untuk session
        }
    }

    /**
     * Menghapus item dari keranjang.
     */
    public function removeItems(array $itemIds): void
    {
        if (Auth::guard('customers')->check()) {
            ProductCart::whereIn('id', $itemIds)
                ->whereHas('cart', fn($q) => $q->where('user_id', Auth::guard('customers')->id()))
                ->delete();
        } else {
            $cart = Session::get('cart', []);
            foreach ($itemIds as $id) {
                unset($cart[$id]);
            }
            Session::put('cart', $cart);
        }
    }

    /**
     * Menghitung jumlah total item di dalam keranjang.
     */
    public function getCartCount(): int
    {
        if (Auth::guard('customers')->check()) {
            $cart = Cart::where('user_id', Auth::guard('customers')->id())->withSum('items', 'quantity')->first();
            return $cart ? (int) $cart->items_sum_quantity : 0;
        }
        $cart = Session::get('cart', []);
        return (int) array_sum(array_column($cart, 'quantity'));
    }

    /**
     * Mengambil item spesifik dari keranjang berdasarkan ID-nya untuk proses checkout.
     */
    public function getItemsByIds(array $itemIds): Collection
    {
        if (empty($itemIds)) {
            return collect();
        }

        if (Auth::guard('customers')->check()) {
            $userId = Auth::guard('customers')->id();
            return ProductCart::whereIn('id', $itemIds)
                ->whereHas('cart', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                })
                ->with(['product', 'variant'])
                ->get();
        } else {
            $sessionCart = Session::get('cart', []);
            if (empty($sessionCart)) {
                return collect();
            }
            $selectedItems = array_filter($sessionCart, function ($variantId) use ($itemIds) {
                return in_array($variantId, $itemIds);
            }, ARRAY_FILTER_USE_KEY);

            if (empty($selectedItems)) {
                return collect();
            }
            $variantIds = array_keys($selectedItems);
            $variants = ProductVariant::with('product')->whereIn('id', $variantIds)->get()->keyBy('id');

            return collect($selectedItems)->map(function ($item) use ($variants) {
                $variant = $variants->get($item['product_variant_id']);
                if (!$variant)
                    return null;
                return (object) [
                    'id' => $item['id'],
                    'cart_id' => 'session',
                    'product_id' => $item['product_id'],
                    'product_variant_id' => $item['product_variant_id'],
                    'quantity' => $item['quantity'],
                    'product' => $variant->product,
                    'variant' => $variant,
                ];
            })->filter();
        }
    }

    public function clearCartItems(array $itemIds): void
    {
        if (Auth::guard('customers')->check()) {
            ProductCart::whereIn('id', $itemIds)
                ->whereHas('cart', fn($q) => $q->where('user_id', Auth::guard('customers')->id()))
                ->delete();
        } else {
            $cart = Session::get('cart', []);
            foreach ($itemIds as $id) {
                unset($cart[$id]);
            }
            Session::put('cart', $cart);
        }
    }
}
