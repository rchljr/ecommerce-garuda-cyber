<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Product; // Pastikan Product diimport jika masih dibutuhkan di sini
use App\Models\Varian; // PENTING: Import Model Varian
use Illuminate\Http\Request;
use App\Services\CartService;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index()
    {
        $cartItems = $this->cartService->getItems();
        return view('customer.cart', compact('cartItems'));
    }

    public function add(Request $request, $subdomain) // $subdomain harus ada di rute
    {
        // --- VALIDASI BARU ---
        $request->validate([
            'product_id' => 'required|exists:products,id', // Tetap butuh product_id untuk konteks
            'variant_id' => 'required|exists:varians,id', // PENTING: Validasi ID varian
            'quantity' => 'required|integer|min:1',
            // Hapus 'size' dan 'color' dari validasi di sini
            // 'size' => 'required|string',
            // 'color' => 'required|string',
        ]);

        try {
            // Temukan varian berdasarkan ID yang dikirim
            $variant = Varian::find($request->variant_id);

            if (!$variant) {
                throw new ModelNotFoundException('Varian produk tidak ditemukan.');
            }

            // Panggil service dengan data yang sudah disesuaikan
            // Kirim objek $variant dan quantity
            $this->cartService->add($variant, $request->quantity);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Produk berhasil ditambahkan!',
                    'cart_count' => $this->cartService->getCartCount(),
                ]);
            }
            return back()->with('success', 'Produk berhasil ditambahkan ke keranjang.');

        } catch (ModelNotFoundException $e) {
            $message = 'Varian produk yang dipilih tidak valid atau tidak ditemukan.';
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $message], 404);
            }
            return back()->with('error', $message);
        } catch (Exception $e) {
            Log::error('Error adding to cart: ' . $e->getMessage(), ['exception' => $e]); // Log exception
            $message = 'Gagal menambahkan produk ke keranjang. Silakan coba lagi.';
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $message], 500);
            }
            return back()->with('error', $message);
        }
    }

    public function update(Request $request, $subdomain, $productCartId)
    {
        $validated = $request->validate(['quantity' => 'required|integer|min:1']);
        $isSuccess = $this->cartService->update($productCartId, $validated['quantity']);
        if (!$isSuccess) {
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui kuantitas.'], 404);
        }
        return response()->json(['success' => true, 'message' => 'Kuantitas berhasil diperbarui.', 'cart_count' => $this->cartService->getCartCount()]);
    }

    public function removeItems(Request $request)
    {
        $validated = $request->validate(['ids' => 'required|array', 'ids.*' => 'string']);
        try {
            $this->cartService->removeItems($validated['ids']);
            return response()->json(['success' => true, 'message' => 'Item berhasil dihapus.', 'cart_count' => $this->cartService->getCartCount()]);
        } catch (Exception $e) {
            Log::error('Cart Deletion Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan.'], 500);
        }
    }
}