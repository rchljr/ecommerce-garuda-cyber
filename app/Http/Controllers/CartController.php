<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Subdomain;
use Illuminate\Http\Request;
use App\Services\CartService;
use Illuminate\Support\Facades\Log;
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
        // ## PERBAIKAN DI SINI ##
        // Secara eksplisit memberitahu validator untuk menggunakan koneksi 'tenant'.
        // Ini memastikan validasi berjalan pada database yang benar.
        $request->validate([
            'product_id' => 'required|exists:tenant.products,id',
            'quantity' => 'required|integer|min:1',
            'size' => 'nullable|string', // Varian sekarang opsional
            'color' => 'nullable|string', // Varian sekarang opsional
        ]);

        // Karena model Product sudah diatur untuk menggunakan koneksi 'tenant',
        // query ini akan berjalan di database yang benar.
        $product = Product::with('variants')->findOrFail($request->product_id);

        // Jika varian tidak dikirim dari frontend (misalnya dari halaman toko),
        // ambil varian pertama sebagai default.
        if (!$request->filled('size') || !$request->filled('color')) {
            $defaultVariant = $product->variants()->first();

            if (!$defaultVariant) {
                // Jika produk tidak punya varian sama sekali
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'Produk ini tidak memiliki varian yang tersedia.'], 404);
                }
                return back()->with('error', 'Produk ini tidak memiliki varian yang tersedia.');
            }
            // Tambahkan varian default ke dalam request
            $request->merge([
                'size' => $defaultVariant->size,
                'color' => $defaultVariant->color,
            ]);
        }

        // Gunakan service untuk menambahkan item ke keranjang
        $this->cartService->add($request);

        // Jika ini adalah permintaan AJAX, kirim kembali respons JSON
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan!',
                'cart_count' => $this->cartService->getCartCount() // Asumsi service punya method ini
            ]);
        }

        return back()->with('success', 'Produk berhasil ditambahkan ke keranjang.');
    }

    /**
     * Menampilkan halaman isi keranjang belanja.
     */
    public function index(Request $request)
    {
        // Ambil data tenant dari request (sudah disiapkan oleh middleware)
        $tenant = $request->get('tenant');

        $cartItems = $this->cartService->getItems($request);
        
        // Pastikan Anda memiliki view yang benar
        // Misalnya: 'templates.template1.cart'
        $templatePath = $tenant->template->path ?? 'default_template_path';

        return view($templatePath . '.cart', compact('tenant', 'cartItems'));
    }

    /**
     * Memperbarui kuantitas item di keranjang.
     */
    public function update(Request $request, $productCartId)
    {
        $validated = $request->validate(['quantity' => 'required|integer|min:1']);

        $this->cartService->update($productCartId, $validated['quantity']);

        // Mengembalikan respons JSON yang konsisten
        return response()->json([
            'success' => true,
            'message' => 'Kuantitas berhasil diperbarui.',
            'cart_count' => $this->cartService->getCartCount()
        ]);
    }

    /**
     * Menghapus item dari keranjang.
     */
    public function removeItems(Request $request)
    {
        try {
            $validated = $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'string',
            ]);

            $this->cartService->removeItems($validated['ids']);

            return response()->json([
                'success' => true,
                'message' => 'Item yang dipilih berhasil dihapus.',
                'cart_count' => $this->cartService->getCartCount()
            ]);
        } catch (Exception $e) {
            // Log error untuk debugging di server
            Log::error('Cart Deletion Error: ' . $e->getMessage());

            // Kirim respons error 500 dengan pesan yang jelas
            return response()->json([
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan pada server saat menghapus item.'
            ], 500);
        }
    }
}