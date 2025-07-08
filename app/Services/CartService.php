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
        $size = $request->input('size') ?? '';
        $color = $request->input('color') ?? '';

        $cartItemId = $productId . '-' . $size . '-' . $color;

        if (Auth::guard('customers')->check()) {
            $cart = Cart::firstOrCreate(['user_id' => Auth::guard('customers')->id()]);

            $item = $cart->items()
                ->where('product_id', $productId)
                ->where('size', $size) // Mencari dengan nilai yang konsisten
                ->where('color', $color) // Mencari dengan nilai yang konsisten
                ->first();

            if ($item) {
                $item->increment('quantity', $quantity);
            } else {
                $cart->items()->create([
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'size' => $size, // Menyimpan nilai yang sudah dinormalisasi
                    'color' => $color, // Menyimpan nilai yang sudah dinormalisasi
                ]);
            }
        } else {
            $cart = Session::get('cart', []);
            $product = Product::find($productId);

            if (isset($cart[$cartItemId])) {
                $cart[$cartItemId]['quantity'] += $quantity;
            } else {
                $cart[$cartItemId] = [
                    "product_id" => $product->id,
                    "name" => $product->name,
                    "quantity" => $quantity,
                    "price" => $product->price,
                    "image" => $product->main_image,
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
        $userId = Auth::guard('customers')->id();

        if ($userId && session()->has('cart')) {
            $sessionCart = Session::get('cart');
            $userCart = Cart::firstOrCreate(['user_id' => $userId]);

            foreach ($sessionCart as $cartItemId => $itemData) {
                // Normalisasi varian dari session sebelum membandingkan dengan database.
                $size = $itemData['size'] ?? '';
                $color = $itemData['color'] ?? '';

                $dbItem = $userCart->items()
                    ->where('product_id', $itemData['product_id'])
                    ->where('size', $size) // Selalu bandingkan string dengan string
                    ->where('color', $color) // Selalu bandingkan string dengan string
                    ->first();

                if ($dbItem) {
                    // Jika item ditemukan, hanya tambah kuantitasnya.
                    $dbItem->increment('quantity', $itemData['quantity']);
                } else {
                    // Jika tidak, buat item baru dengan data yang sudah dinormalisasi.
                    $userCart->items()->create([
                        'product_id' => $itemData['product_id'],
                        'quantity' => $itemData['quantity'],
                        'size' => $size,
                        'color' => $color,
                    ]);
                }
            }

            // Hapus dan simpan session setelah selesai.
            session()->forget('cart');
            session()->save();
        }
    }
    /**
     * Mengambil semua item dari keranjang untuk toko saat ini.
     */
    public function getItems(Request $request)
    {
        $tenant = $request->get('tenant');

        if (Auth::guard('customers')->check()) {
            $cart = Cart::where('user_id', Auth::guard('customers')->id())->first();
            if (!$cart)
                return collect();

            // Asumsi: Produk tidak perlu difilter per tenant di sini karena sudah terikat pada user
            return $cart->items()->with(['product.shopOwner.shop'])->get();
        } else {
            $sessionCart = Session::get('cart', []);
            return collect($sessionCart)->map(function ($item, $cartItemId) {
                $product = Product::with(['shopOwner.shop'])->find($item['product_id']);
                if ($product) {
                    $item['id'] = $cartItemId;
                    $item['product'] = $product;
                    return (object) $item;
                }
                return null;
            })->filter();
        }
    }

    public function getItemsByIds(Request $request, array $itemIds)
    {
        $tenant = $request->get('tenant');
        if (!$tenant) {
            return collect();
        }

        if (Auth::guard('customers')->check()) {
            $cart = Cart::where('user_id', Auth::guard('customers')->id())->first();
            if (!$cart) {
                return collect();
            }

            // Ambil item dari keranjang berdasarkan ID yang dipilih DAN milik tenant saat ini.
            return $cart->items()
                ->whereIn('id', $itemIds) // Filter berdasarkan ID yang dipilih
                ->with(['product.shopOwner.shop'])
                ->whereHas('product.shopOwner.shop', function ($query) use ($tenant) {
                    $query->where('id', $tenant->id);
                })
                ->get();
        } else {
            $sessionCart = Session::get('cart', []);
            $tenantId = $tenant->id;

            // Filter array session utama untuk hanya menyertakan kunci yang ada di $itemIds
            $selectedItems = array_intersect_key($sessionCart, array_flip($itemIds));

            // Sekarang, map dan filter item yang sudah dipilih berdasarkan tenant
            $filteredCart = collect($selectedItems)->map(function ($item, $cartItemId) use ($tenantId) {
                $product = Product::with(['shopOwner.shop'])->find($item['product_id']);
                
                if ($product && optional(optional($product->shopOwner)->shop)->id == $tenantId) {
                    $item['id'] = $cartItemId;
                    $item['product'] = $product;
                    return (object) $item;
                }
                
                return null;
            })->filter();

            return $filteredCart;
        }
    }
    /**
     * Menghitung jumlah total item di dalam keranjang.
     *
     * @return int
     */
    public function getCartCount(): int
    {
        if (Auth::guard('customers')->check()) {
            $cart = Cart::where('user_id', Auth::guard('customers')->id())->with('items')->first();
            return $cart ? $cart->items->sum('quantity') : 0;
        } else {
            $cart = Session::get('cart', []);
            return array_sum(array_column($cart, 'quantity'));
        }
    }
    /**
     * Memperbarui kuantitas item.
     */
    public function update(string $productCartId, int $quantity)
    {
        if (Auth::guard('customers')->check()) {
            // Logika untuk pengguna login (database)
            $userId = Auth::guard('customers')->id();
            // Dapatkan ID keranjang milik pengguna
            $cartId = Cart::where('user_id', $userId)->value('id');

            if ($cartId) {
                // Lakukan query langsung ke model ProductCart. Ini lebih andal.
                $item = ProductCart::where('cart_id', $cartId)
                    ->where('id', $productCartId)
                    ->first();

                // Jika item ditemukan, perbarui kuantitasnya dan simpan.
                if ($item) {
                    $item->quantity = $quantity;
                    $item->save();
                }
            }
        } else {
            // Logika untuk tamu (session)
            $cart = Session::get('cart', []);
            if (isset($cart[$productCartId])) {
                $cart[$productCartId]['quantity'] = $quantity;
                Session::put('cart', $cart);
                // Paksa session untuk menyimpan perubahan
                Session::save();
            }
        }
    }
    /**
     * Menghapus item dari keranjang.
     */
    public function removeItems(array $itemIds)
    {
        if (Auth::guard('customers')->check()) {
            $userId = Auth::guard('customers')->id();
            // Dapatkan ID keranjang pengguna
            $cartId = Cart::where('user_id', $userId)->value('id');

            if ($cartId) {
                // Hapus item ProductCart secara langsung. Ini lebih aman dan
                // memastikan kita hanya menghapus item dari keranjang milik pengguna.
                ProductCart::where('cart_id', $cartId)
                    ->whereIn('id', $itemIds)
                    ->delete();
            }
        } else {
            // Logika session sudah benar, pastikan untuk menyimpan di akhir.
            foreach ($itemIds as $id) {
                Session::forget("cart.{$id}");
            }
            Session::save();
        }
    }
}