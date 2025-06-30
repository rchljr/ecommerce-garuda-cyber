<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category; // Pastikan Category di-import jika digunakan di controller lain
use Illuminate\Support\Facades\Session; // Import Session facade

class CartController extends Controller
{
    /**
     * Menambahkan produk ke keranjang via AJAX.
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        // Dapatkan cart dari session, atau buat array kosong jika belum ada
        $cart = Session::get('cart', []);

        // Jika produk sudah ada di cart, tambahkan kuantitasnya
        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] += $request->quantity;
        } else {
            // Jika belum ada, tambahkan sebagai item baru
            $cart[$product->id] = [
                "id" => $product->id,
                "name" => $product->name,
                "quantity" => $request->quantity,
                "price" => $product->price,
                "image" => $product->image_url // Pastikan accessor image_url ada
            ];
        }
        
        // Simpan kembali cart ke dalam session
        Session::put('cart', $cart);

        // Hitung jumlah item unik di keranjang
        $cartCount = count($cart);

        // === PERUBAHAN UTAMA DI SINI ===
        // Tambahkan 'success' => true agar sesuai dengan yang diharapkan oleh JavaScript
        return response()->json([
            'success' => true, 
            'message' => 'Produk berhasil ditambahkan!',
            'cart_count' => $cartCount
        ]);
    }

    /**
     * Menampilkan halaman isi keranjang belanja.
     */
    public function index()
    {
        $cartItems = Session::get('cart', []);
        return view('template1.cart', compact('cartItems')); // Pastikan nama viewnya benar
    }

    /**
     * Memperbarui kuantitas item di keranjang.
     */
    public function update(Request $request, string $product_id)
    {
        $request->validate(['quantity' => 'required|integer|min:0']);

        $cart = Session::get('cart', []);

        if (isset($cart[$product_id])) {
            if ($request->quantity == 0) {
                unset($cart[$product_id]);
                Session::put('cart', $cart);
                return redirect()->route('cart.index')->with('success', 'Produk berhasil dihapus.');
            } else {
                $cart[$product_id]['quantity'] = $request->quantity;
                Session::put('cart', $cart);
                return redirect()->route('cart.index')->with('success', 'Kuantitas berhasil diperbarui.');
            }
        }
        return redirect()->route('cart.index')->with('error', 'Produk tidak ditemukan.');
    }

    /**
     * Menghapus item dari keranjang.
     */
    public function remove(string $product_id)
    {
        $cart = Session::get('cart', []);

        if (isset($cart[$product_id])) {
            unset($cart[$product_id]);
            Session::put('cart', $cart);
            return redirect()->route('cart.index')->with('success', 'Produk berhasil dihapus.');
        }

        return redirect()->route('cart.index')->with('error', 'Produk tidak ditemukan.');
    }
}
