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
use Illuminate\Support\Facades\Storage;

class CartService
{
    public function add(Varian $variant, int $quantity): void
    {
        if (!$variant->relationLoaded('product')) {
            $variant->load('product');
        }

        if ($variant->stock < $quantity) {
            throw new Exception('Stok varian "' . $variant->name . '" tidak mencukupi. Stok tersedia: ' . $variant->stock);
        }

        if (Auth::guard('customers')->check()) {
            $this->addToDatabase($variant, $quantity);
        } else {
            $this->addToSession($variant, $quantity);
        }
    }

    private function addToDatabase(Varian $variant, int $quantity): void
    {
        $userId = Auth::guard('customers')->id();
        $cart = Cart::firstOrCreate(['user_id' => $userId]);

        DB::transaction(function () use ($cart, $variant, $quantity) {
            $item = $cart->items()->where('product_variant_id', $variant->id)->first();

            if ($item) {
                if (($item->quantity + $quantity) > $variant->stock) {
                    throw new Exception('Penambahan melebihi stok untuk varian "' . $variant->name . '".');
                }
                $item->increment('quantity', $quantity);
            } else {
                $cart->items()->create([
                    'product_id' => $variant->product->id,
                    'product_variant_id' => $variant->id,
                    'quantity' => $quantity,
                ]);
            }
        });
    }

    private function addToSession(Varian $variant, int $quantity): void
    {
        $cart = Session::get('cart', []);
        $cartItemId = $variant->id;

        if (isset($cart[$cartItemId])) {
            if (($cart[$cartItemId]['quantity'] + $quantity) > $variant->stock) {
                throw new Exception('Penambahan melebihi stok untuk varian "' . $variant->name . '".');
            }
            $cart[$cartItemId]['quantity'] += $quantity;
        } else {
            $cart[$cartItemId] = [
                'product_id' => $variant->product->id,
                'variant_id' => $variant->id,
                'quantity' => $quantity,
                'added_at' => now()->toIso8601String(),
            ];
        }

        Session::put('cart', $cart);
    }

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

        $userCart = Cart::firstOrCreate(['user_id' => $userId]);

        DB::beginTransaction();
        try {
            foreach ($sessionCart as $cartItemId => $sessionItem) {
                if (!isset($sessionItem['variant_id']))
                    continue;

                $variant = Varian::find($sessionItem['variant_id']);
                if (!$variant)
                    continue;

                $dbItem = $userCart->items()->where('product_variant_id', $sessionItem['variant_id'])->first();

                if ($dbItem) {
                    $newQuantity = $dbItem->quantity + $sessionItem['quantity'];
                    $dbItem->quantity = min($newQuantity, $variant->stock);
                    $dbItem->save();
                } else {
                    $userCart->items()->create([
                        'product_id' => $sessionItem['product_id'],
                        'product_variant_id' => $sessionItem['variant_id'],
                        'quantity' => min($sessionItem['quantity'], $variant->stock),
                    ]);
                }
            }
            DB::commit();
            Session::forget('cart');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Failed to merge cart for user {$userId}: " . $e->getMessage());
        }
    }


    public function getItems(): Collection
    {
        if (Auth::guard('customers')->check()) {
            $cart = Cart::where('user_id', Auth::guard('customers')->id())->first();
            if (!$cart) {
                return collect();
            }

            return $cart->items()
                ->with(['product.shopOwner.shop', 'product.shopOwner.subdomain', 'variant'])
                ->get()
                ->map(function ($item) {
                    if (!$item->variant || !$item->product)
                        return null;

                    $item->id_for_cart = $item->id;
                    $item->image = $item->variant->image_path ? Storage::url($item->variant->image_path) : ($item->product->main_image ? Storage::url($item->product->main_image) : null);
                    $item->price = $item->variant->price;
                    $item->stock = $item->variant->stock;
                    return $item;
                })->filter();
        }

        $sessionCart = Session::get('cart', []);
        if (empty($sessionCart)) {
            return collect();
        }

        $variantIds = array_keys($sessionCart);
        $variants = Varian::with(['product.shopOwner.shop', 'product.shopOwner.subdomain'])->whereIn('id', $variantIds)->get()->keyBy('id');

        return collect($sessionCart)->map(function ($itemData, $variantId) use ($variants) {
            $variant = $variants->get($variantId);
            if (!$variant || !$variant->product)
                return null;

            $imageUrl = $variant->image_path ? Storage::url($variant->image_path) : ($variant->product->main_image ? Storage::url($variant->product->main_image) : null);

            return (object) [
                'id_for_cart' => $variant->id,
                'product_id' => $variant->product->id,
                'product_variant_id' => $variant->id,
                'quantity' => $itemData['quantity'],
                'product' => $variant->product,
                'variant' => $variant,
                'image' => $imageUrl,
                'price' => $variant->price,
                'stock' => $variant->stock,
            ];
        })->filter();
    }

    public function update(string $itemId, int $quantity): bool
    {
        if (Auth::guard('customers')->check()) {
            $item = ProductCart::with('variant')->find($itemId);

            if (!$item || !$item->variant || $item->cart->user_id !== Auth::guard('customers')->id()) {
                throw new Exception('Item tidak ditemukan atau tidak sah.');
            }

            if ($quantity > $item->variant->stock) {
                throw new Exception('Kuantitas melebihi stok yang tersedia (' . $item->variant->stock . ').');
            }

            $item->quantity = $quantity;
            return $item->save();
        } else {
            $cart = Session::get('cart', []);
            if (isset($cart[$itemId])) {
                $variant = Varian::find($itemId);
                if (!$variant)
                    throw new Exception('Varian tidak ditemukan.');

                if ($quantity > $variant->stock) {
                    throw new Exception('Kuantitas melebihi stok yang tersedia (' . $variant->stock . ').');
                }

                $cart[$itemId]['quantity'] = $quantity;
                Session::put('cart', $cart);
                return true;
            }
            throw new Exception('Item tidak ditemukan di keranjang sesi.');
        }
    }

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

    public function getCartCount(): int
    {
        if (Auth::guard('customers')->check()) {
            $cart = Cart::where('user_id', Auth::guard('customers')->id())->withSum('items', 'quantity')->first();
            return $cart ? (int) $cart->items_sum_quantity : 0;
        }
        return (int) array_sum(array_column(Session::get('cart', []), 'quantity'));
    }

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
                ->with([
                    'variant',
                    'product.shopOwner.shop',
                    'product.shopOwner.subdomain'
                ])
                ->get();
        }

        $sessionCart = Session::get('cart', []);
        if (empty($sessionCart)) {
            return collect();
        }

        $selectedSessionItems = array_filter($sessionCart, fn($key) => in_array($key, $itemIds), ARRAY_FILTER_USE_KEY);
        $variantIds = array_column($selectedSessionItems, 'variant_id');

        $variants = Varian::with(['product.shopOwner.shop', 'product.shopOwner.subdomain'])->whereIn('id', $variantIds)->get()->keyBy('id');

        return collect($selectedSessionItems)->map(function ($itemData) use ($variants) {
            $variant = $variants->get($itemData['variant_id']);
            if (!$variant)
                return null;

            return (object) [
                'id' => $variant->id, // Untuk guest, ID item adalah ID varian
                'product_id' => $variant->product_id,
                'product_variant_id' => $variant->id,
                'quantity' => $itemData['quantity'],
                'product' => $variant->product,
                'variant' => $variant,
            ];
        })->filter();
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
