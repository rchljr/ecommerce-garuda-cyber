<?php

namespace App\Services;

use Exception;
use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductCart;
use App\Models\Varian;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage; // Pastikan ini diimpor

class CartService
{
    // Hapus constructor jika ada di sini. Service ini tidak membutuhkannya.
    // Jika Anda memiliki `protected $cartService;` juga, hapus itu juga.

    /**
     * Menambahkan varian produk ke keranjang.
     * Logika akan memilih antara session (guest) atau database (logged in user).
     *
     * @param Varian $variant Objek varian yang akan ditambahkan.
     * @param int $quantity Jumlah yang akan ditambahkan.
     * @throws Exception Jika stok tidak mencukupi atau ada masalah lain.
     */
    public function add(Varian $variant, int $quantity): void
    {
        // Pastikan varian memiliki relasi produk yang dimuat untuk mendapatkan info produk
        if (!$variant->relationLoaded('product')) {
            $variant->load('product');
        }

        // Cek stok awal
        if ($variant->stock < $quantity) {
            throw new Exception('Stok varian "' . $variant->name . '" tidak mencukupi. Stok tersedia: ' . $variant->stock);
        }

        if (Auth::guard('customers')->check()) {
            // Jika pengguna login, tambahkan ke database
            $this->addToDatabase($variant, $quantity);
        } else {
            // Jika tamu, tambahkan ke session
            $this->addToSession($variant, $quantity);
        }
    }

    /**
     * Menambahkan item ke keranjang di database untuk pengguna yang login.
     *
     * @param Varian $variant
     * @param int $quantity
     * @throws Exception Jika stok tidak mencukupi.
     */
    private function addToDatabase(Varian $variant, int $quantity): void
    {
        $userId = Auth::guard('customers')->id();
        $cart = Cart::firstOrCreate(['user_id' => $userId]);

        DB::transaction(function () use ($cart, $variant, $quantity) {
            $item = $cart->items()->where('product_variant_id', $variant->id)->first();

            if ($item) {
                // Item sudah ada, periksa stok sebelum increment
                if (($item->quantity + $quantity) > $variant->stock) {
                    throw new Exception('Penambahan melebihi stok maksimal untuk varian "' . $variant->name . '".');
                }
                $item->increment('quantity', $quantity);
            } else {
                // Item belum ada
                $cart->items()->create([
                    'product_id' => $variant->product->id,
                    'product_variant_id' => $variant->id,
                    'quantity' => $quantity,
                ]);
            }
        });
    }

    /**
     * Menambahkan item ke keranjang di session untuk pengguna tamu.
     *
     * @param Varian $variant
     * @param int $quantity
     * @throws Exception Jika stok tidak mencukupi.
     */
    private function addToSession(Varian $variant, int $quantity): void
    {
        $cart = Session::get('cart', []);
        $cartItemId = $variant->product->id . '_' . $variant->id; // Kunci unik untuk item keranjang sesi

        if (isset($cart[$cartItemId])) {
            // Item sudah ada di sesi, periksa stok sebelum increment
            if (($cart[$cartItemId]['quantity'] + $quantity) > $variant->stock) {
                throw new Exception('Penambahan melebihi stok maksimal untuk varian "' . $variant->name . '".');
            }
            $cart[$cartItemId]['quantity'] += $quantity;
        } else {
            // Tentukan URL gambar untuk keranjang sesi
            $imageUrl = null;
            if ($variant->image_path) {
                $imageUrl = Storage::url($variant->image_path);
            } elseif ($variant->product->main_image) {
                $imageUrl = Storage::url($variant->product->main_image);
            }

            $cart[$cartItemId] = [
                'product_id' => $variant->product->id,
                'variant_id' => $variant->id,
                'name' => $variant->product->name . ' (' . $variant->name . ')', // Nama produk + nama varian
                'quantity' => $quantity,
                'price' => $variant->price,
                'image' => $imageUrl,
            ];
        }

        Session::put('cart', $cart);
    }

    /**
     * Menggabungkan keranjang dari session ke database setelah login.
     * Ini akan dipanggil di middleware atau saat login.
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

        DB::beginTransaction();
        try {
            foreach ($sessionCart as $cartItemId => $sessionItem) {
                if (!isset($sessionItem['product_id']) || !isset($sessionItem['quantity']) || !isset($sessionItem['variant_id'])) {
                    continue;
                }

                $variant = Varian::find($sessionItem['variant_id']);

                if (!$variant) {
                    Log::warning("Skipping session cart item (variant not found): " . $cartItemId);
                    continue;
                }

                $dbItem = $userCart->items()->where('product_variant_id', $sessionItem['variant_id'])->first();

                if ($dbItem) {
                    $newQuantity = $dbItem->quantity + $sessionItem['quantity'];
                    if ($newQuantity > $variant->stock) {
                        $dbItem->quantity = $variant->stock;
                        Log::warning("Merged quantity for variant {$variant->id} exceeded stock. Limited to {$variant->stock}.");
                    } else {
                        $dbItem->quantity = $newQuantity;
                    }
                    $dbItem->save();
                } else {
                    $userCart->items()->create([
                        'cart_id' => $userCart->id, // Pastikan cart_id terisi
                        'product_id' => $sessionItem['product_id'],
                        'product_variant_id' => $sessionItem['variant_id'],
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
     * Akan memilih antara data database (logged in) atau session (guest).
     *
     * @return Collection
     */
    public function getItems(): Collection
    {
        if (Auth::guard('customers')->check()) {
            $cart = Cart::where('user_id', Auth::guard('customers')->id())->first();
            if (!$cart) {
                return collect();
            }

            return $cart->items()
                ->with(['product', 'variant'])
                ->get()
                ->map(function ($item) {
                    $item->image = $item->variant->image_path ? Storage::url($item->variant->image_path) : ($item->product->main_image ? Storage::url($item->product->main_image) : null);
                    $item->full_name = $item->product->name . ' (' . ($item->variant->name ?? '') . ')';
                    return $item;
                });
        }

        // --- Logika untuk Tamu (Guest) ---
        $sessionCart = Session::get('cart', []);
        if (empty($sessionCart)) {
            return collect();
        }

        $variantIds = collect($sessionCart)->pluck('variant_id')->filter()->unique()->toArray();

        $variants = Varian::with('product')
            ->whereIn('id', $variantIds)
            ->get()
            ->keyBy('id');

        return collect($sessionCart)->map(function ($item) use ($variants) {
            $variant = $variants->get($item['variant_id']);

            if (!$variant || !$variant->product) {
                Log::warning("Skipping session item in getItems: Variant or Product not found for variant_id " . ($item['variant_id'] ?? 'N/A'));
                return null;
            }

            $imageUrl = null;
            if ($variant->image_path) {
                $imageUrl = Storage::url($variant->image_path);
            } elseif ($variant->product->main_image) {
                $imageUrl = Storage::url($variant->product->main_image);
            }

            return (object) [
                'id' => $item['variant_id'],
                'cart_id' => 'session',
                'product_id' => $variant->product->id,
                'product_variant_id' => $variant->id,
                'quantity' => $item['quantity'],
                'product' => $variant->product,
                'variant' => $variant,
                'created_at' => $item['added_at'] ?? now()->toIso8601String(),
                'image' => $imageUrl,
                'full_name' => $variant->product->name . ' (' . ($variant->name ?? '') . ')',
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
            $item = ProductCart::with('cart', 'variant')->find($itemId);

            if (!$item || !$item->cart || $item->cart->user_id !== $userId) {
                Log::warning("Cart update failed: ProductCart item with ID {$itemId} not found or unauthorized.");
                return false;
            }

            if (!$item->variant) {
                 Log::warning("Cart update failed: Variant for ProductCart ID {$itemId} not found.");
                 return false;
            }

            if ($quantity > $item->variant->stock) {
                Log::warning("Cart update failed: Quantity {$quantity} for item {$itemId} exceeds stock {$item->variant->stock}.");
                $item->quantity = $item->variant->stock;
                $item->save();
                return false;
            }

            $item->quantity = $quantity;
            $item->save();
            Log::info("Successfully updated quantity for ProductCart ID: {$itemId}");
            return true;
        } else {
            $cart = Session::get('cart', []);
            if (isset($cart[$itemId])) {
                $variant = Varian::find($cart[$itemId]['variant_id']);
                if (!$variant) {
                    Log::warning("Session cart update failed: Variant with ID {$cart[$itemId]['variant_id']} not found for session item {$itemId}.");
                    unset($cart[$itemId]);
                    Session::put('cart', $cart);
                    return false;
                }

                if ($quantity > $variant->stock) {
                    Log::warning("Session cart update failed: Quantity {$quantity} for session item {$itemId} exceeds stock {$variant->stock}.");
                    $cart[$itemId]['quantity'] = $variant->stock;
                    Session::put('cart', $cart);
                    return false;
                }

                $cart[$itemId]['quantity'] = $quantity;
                Session::put('cart', $cart);
                return true;
            }
            return false;
        }
    }

    /**
     * Menghapus item dari keranjang.
     * $itemIds adalah array dari ID ProductCart (DB) atau kunci varian (Session).
     */
    public function removeItems(array $itemIds): void
    {
        if (Auth::guard('customers')->check()) {
            ProductCart::whereIn('id', $itemIds)
                ->whereHas('cart', fn($q) => $q->where('user_id', Auth::guard('customers')->id()))
                ->delete();
            Log::info("Successfully removed ProductCart items from DB: " . implode(', ', $itemIds));
        } else {
            $cart = Session::get('cart', []);
            foreach ($itemIds as $id) {
                if (isset($cart[$id])) {
                    unset($cart[$id]);
                    Log::info("Successfully removed session cart item: " . $id);
                }
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
     * Mengambil dari DB (logged in) atau Session (guest).
     *
     * @param array $itemIds Array dari ID ProductCart (DB) atau kunci varian (Session) yang dipilih.
     * @return Collection
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
                ->get()
                ->map(function ($item) {
                    $item->image = $item->variant->image_path ? Storage::url($item->variant->image_path) : ($item->product->main_image ? Storage::url($item->product->main_image) : null);
                    $item->full_name = $item->product->name . ' (' . ($item->variant->name ?? '') . ')';
                    return $item;
                });
        } else {
            $sessionCart = Session::get('cart', []);
            if (empty($sessionCart)) {
                return collect();
            }

            $selectedSessionItems = array_filter($sessionCart, function ($key) use ($itemIds) {
                return in_array($key, $itemIds);
            }, ARRAY_FILTER_USE_KEY);

            if (empty($selectedSessionItems)) {
                return collect();
            }

            $variantIdsToFetch = collect($selectedSessionItems)->pluck('variant_id')->filter()->unique()->toArray();

            $variants = Varian::with('product')
                ->whereIn('id', $variantIdsToFetch)
                ->get()
                ->keyBy('id');

            return collect($selectedSessionItems)->map(function ($item) use ($variants) {
                $variant = $variants->get($item['variant_id']);

                if (!$variant || !$variant->product) {
                    Log::warning("Skipping session item in getItemsByIds: Variant or Product not found for variant_id " . ($item['variant_id'] ?? 'N/A'));
                    return null;
                }

                $imageUrl = null;
                if ($variant->image_path) {
                    $imageUrl = Storage::url($variant->image_path);
                } elseif ($variant->product->main_image) {
                    $imageUrl = Storage::url($variant->product->main_image);
                }

                return (object) [
                    'id' => $item['variant_id'],
                    'cart_id' => 'session',
                    'product_id' => $variant->product->id,
                    'product_variant_id' => $variant->id,
                    'quantity' => $item['quantity'],
                    'product' => $variant->product,
                    'variant' => $variant,
                    'created_at' => $item['added_at'] ?? now()->toIso8601String(),
                    'image' => $imageUrl,
                    'full_name' => $variant->product->name . ' (' . ($variant->name ?? '') . ')',
                ];
            })->filter()
              ->sortByDesc('created_at');
        }
    }

    /**
     * Menghapus semua item dari keranjang tertentu.
     * Digunakan setelah checkout atau pembatalan order.
     *
     * @param array $itemIds Array dari ID ProductCart (DB) atau kunci varian (Session).
     */
    public function clearCartItems(array $itemIds): void
    {
        if (Auth::guard('customers')->check()) {
            ProductCart::whereIn('id', $itemIds)
                ->whereHas('cart', fn($q) => $q->where('user_id', Auth::guard('customers')->id()))
                ->delete();
            Log::info("Successfully cleared ProductCart items from DB: " . implode(', ', $itemIds));
        } else {
            $cart = Session::get('cart', []);
            foreach ($itemIds as $id) {
                if (isset($cart[$id])) {
                    unset($cart[$id]);
                    Log::info("Successfully cleared session cart item: " . $id);
                }
            }
            Session::put('cart', $cart);
        }
    }
}