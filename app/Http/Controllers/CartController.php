<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    /**
     * Menambahkan produk beserta variannya ke keranjang.
     */
    public function add(Request $request)
    {
        // 1. Validasi input, sekarang termasuk ukuran dan warna
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'size' => 'required|string', // Varian ukuran wajib diisi
            'color' => 'required|string', // Varian warna wajib diisi
        ]);

        $product = Product::findOrFail($request->product_id);

        // 2. Buat ID unik untuk item di keranjang berdasarkan varian
        // Contoh: 'uuid-L-Merah'
        $cartItemId = $product->id . '-' . $request->size . '-' . $request->color;

        $cart = Session::get('cart', []);

        // 3. Cek apakah item dengan varian yang sama sudah ada di keranjang
        if (isset($cart[$cartItemId])) {
            // Jika sudah ada, cukup tambahkan kuantitasnya
            $cart[$cartItemId]['quantity'] += $request->quantity;
        } else {
            // Jika belum ada, tambahkan sebagai item baru
            $cart[$cartItemId] = [
                "id" => $product->id, // Simpan ID produk asli
                "name" => $product->name,
                "quantity" => $request->quantity,
                "price" => $product->price,
                "image" => $product->image_url,
                "size" => $request->size,   // Simpan ukuran
                "color" => $request->color, // Simpan warna
            ];
        }
        
        Session::put('cart', $cart);

        return response()->json([
            'success' => true, 
            'message' => 'Produk berhasil ditambahkan!',
            'cart_count' => count($cart)
        ]);
    }

    /**
     * Menampilkan halaman isi keranjang belanja.
     */
    public function index()
    {
        $cartItems = Session::get('cart', []);
        return view('template1.cart', compact('cartItems'));
    }

    /**
     * Memperbarui kuantitas item di keranjang.
     */
    public function update(Request $request, string $cartItemId) // Sekarang menggunakan cartItemId
    {
        $request->validate(['quantity' => 'required|integer|min:1']);

        $cart = Session::get('cart', []);

        if (isset($cart[$cartItemId])) {
            $cart[$cartItemId]['quantity'] = $request->quantity;
            Session::put('cart', $cart);
            return redirect()->route('cart.index')->with('success', 'Kuantitas berhasil diperbarui.');
        }

        return redirect()->route('cart.index')->with('error', 'Produk tidak ditemukan di keranjang.');
    }

    /**
     * Menghapus item dari keranjang.
     */
    public function remove(string $cartItemId) // Sekarang menggunakan cartItemId
    {
        $cart = Session::get('cart', []);

        if (isset($cart[$cartItemId])) {
            unset($cart[$cartItemId]);
            Session::put('cart', $cart);
            return redirect()->route('cart.index')->with('success', 'Produk berhasil dihapus.');
        }

        return redirect()->route('cart.index')->with('error', 'Produk tidak ditemukan di keranjang.');
    }
}
