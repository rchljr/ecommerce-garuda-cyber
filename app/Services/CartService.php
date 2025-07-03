<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductCart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartService
{
    /**
     * Menambahkan produk ke keranjang (Session atau Database).
     */
    public function add(Request $request)
    {
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity', 1);
        $size = $request->input('size');
        $color = $request->input('color');

        $cartItemId = $productId . '-' . $size . '-' . $color;

        if (Auth::check()) {
            // === LOGIKA UNTUK PENGGUNA YANG SUDAH LOGIN (DATABASE) ===
            $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);

            // Cari item berdasarkan produk DAN varian yang sama
            $item = $cart->items()
                ->where('product_id', $productId)
                ->where('size', $size)
                ->where('color', $color)
                ->first();

            if ($item) {
                // Jika sudah ada, cukup tambahkan kuantitasnya
                $item->increment('quantity', $quantity);
            } else {
                // Jika belum ada, tambahkan sebagai item baru
                $cart->items()->create([
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'size' => $size,
                    'color' => $color,
                ]);
            }
        } else {
            // === LOGIKA UNTUK PENGGUNA TAMU (SESSION) ===
            $cart = Session::get('cart', []);
            $product = Product::find($productId); // Ambil detail produk

            if (isset($cart[$cartItemId])) {
                // Jika sudah ada, tambahkan kuantitasnya
                $cart[$cartItemId]['quantity'] += $quantity;
            } else {
                // Jika belum ada, tambahkan sebagai item baru
                $cart[$cartItemId] = [
                    "product_id" => $product->id,
                    "name" => $product->name,
                    "quantity" => $quantity,
                    "price" => $product->price,
                    "image" => $product->image_url, // Asumsi ada properti image_url
                    "size" => $size,
                    "color" => $color,
                ];
            }
            Session::put('cart', $cart);
        }
    }

    /**
     * Menggabungkan keranjang dari session ke database setelah login.
     */
    public function mergeSessionCart()
    {
        if (session()->has('cart')) {
            $sessionCart = Session::get('cart');
            $userCart = Cart::firstOrCreate(['user_id' => Auth::id()]);

            foreach ($sessionCart as $cartItemId => $itemData) {
                // Cari item di database dengan produk dan varian yang sama
                $dbItem = $userCart->items()
                    ->where('product_id', $itemData['product_id'])
                    ->where('size', $itemData['size'])
                    ->where('color', $itemData['color'])
                    ->first();

                if ($dbItem) {
                    // Jika item sudah ada di DB, tambahkan kuantitasnya
                    $dbItem->increment('quantity', $itemData['quantity']);
                } else {
                    // Jika tidak, buat item baru
                    $userCart->items()->create([
                        'product_id' => $itemData['product_id'],
                        'quantity' => $itemData['quantity'],
                        'size' => $itemData['size'],
                        'color' => $itemData['color'],
                    ]);
                }
            }

            // Hapus keranjang dari session setelah digabungkan
            session()->forget('cart');
        }
    }
    /**
     * Mengambil semua item dari keranjang untuk toko saat ini.
     */
    public function getItems(Request $request)
    {
        $subdomainName = explode('.', $request->getHost())[0];

        if (Auth::check()) {
            $cart = Cart::where('user_id', Auth::id())->first();
            if (!$cart)
                return collect();

            return $cart->items()
                ->with(['product.shopOwner.shop'])
                ->whereHas('product.shopOwner.subdomain', function ($query) use ($subdomainName) {
                    $query->where('subdomain_name', $subdomainName);
                })
                ->get();
        } else {
            $sessionCart = Session::get('cart', []);
            $productIds = array_column($sessionCart, 'product_id');

            // Ambil produk dan filter berdasarkan subdomain
            $products = Product::with(['shopOwner.shop', 'shopOwner.subdomain'])
                ->whereIn('id', $productIds)
                ->whereHas('shopOwner.subdomain', function ($query) use ($subdomainName) {
                    $query->where('subdomain_name', $subdomainName);
                })
                ->get()->keyBy('id');

            return collect($sessionCart)->map(function ($item, $cartItemId) use ($products) {
                $product = $products->get($item['product_id']);
                if ($product) {
                    $item['id'] = $cartItemId; // Gunakan composite key sebagai ID untuk session
                    $item['product'] = $product;
                    return (object) $item;
                }
                return null;
            })->filter();
        }
    }
    /**
     * Memperbarui kuantitas item.
     */
    public function update(string $productCartId, int $quantity)
    {
        if (Auth::check()) {
            ProductCart::where('id', $productCartId)
                ->whereHas('cart', fn($q) => $q->where('user_id', Auth::id()))
                ->update(['quantity' => $quantity]);
        } else {
            $cart = Session::get('cart', []);
            if (isset($cart[$productCartId])) {
                $cart[$productCartId]['quantity'] = $quantity;
                Session::put('cart', $cart);
            }
        }
    }
    /**
     * Menghapus item dari keranjang.
     */
    public function remove(string $productCartId)
    {
        if (Auth::check()) {
            ProductCart::where('id', $productCartId)
                ->whereHas('cart', fn($q) => $q->where('user_id', Auth::id()))
                ->delete();
        } else {
            $cart = Session::get('cart', []);
            unset($cart[$productCartId]);
            Session::put('cart', $cart);
        }
    }
}