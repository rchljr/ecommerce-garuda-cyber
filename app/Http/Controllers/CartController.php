<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Models\Subdomain;
use Illuminate\Http\Request;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService) 
    {
        $this->cartService = $cartService;
    }
    /**
     * Menambahkan produk beserta variannya ke keranjang.
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'size' => 'required|string', // Varian ukuran wajib diisi
            'color' => 'required|string', // Varian warna wajib diisi
        ]);

        // Gunakan service untuk menambahkan item ke keranjang
        $this->cartService->add($request);

        return back()->with('success', 'Produk berhasil ditambahkan ke keranjang.');
    }

    /**
     * Menampilkan halaman isi keranjang belanja.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        // Ambil subdomain toko saat ini
        $subdomainName = explode('.', $request->getHost())[0];
        $subdomain = Subdomain::where('subdomain_name', $subdomainName)->first();

        // Ambil item di keranjang HANYA dari toko yang sedang dikunjungi
        $cartItems = $cart->items()
            ->with('product.shopOwner.shop')
            ->whereHas('product.shopOwner.subdomain', function ($query) use ($subdomainName) {
                $query->where('subdomain_name', $subdomainName);
            })
            ->get();

        return view('customer.cart', compact('cartItems'));
    }

    /**
     * Memperbarui kuantitas item di keranjang.
     */
    public function update(Request $request, $productCartId)
    {
        $validated = $request->validate(['quantity' => 'required|integer|min:1']);
        $this->cartService->update($productCartId, $validated['quantity']);
        return back()->with('success', 'Kuantitas berhasil diperbarui.');
    }

    /**
     * Menghapus item dari keranjang.
     */
    public function remove($productCartId)
    {
        $this->cartService->remove($productCartId);
        return back()->with('success', 'Produk berhasil dihapus dari keranjang.');
    }
}
