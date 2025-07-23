<?php

namespace App\Http\Controllers;

use Exception;
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
        $currentSubdomain = request()->route('subdomain');
        // PERBAIKAN: Mengirim subdomain ke view untuk digunakan di rute
        return view('customer.cart', compact('cartItems', 'currentSubdomain'));
    }

    public function add(Request $request, $subdomain)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'required|exists:varians,id',
            'quantity' => 'required|integer|min:1',
        ]);

        try {
            $variant = Varian::findOrFail($request->variant_id);

            // PERBAIKAN: Validasi stok sebelum menambahkan ke keranjang
            if ($variant->stock < $request->quantity) {
                $message = 'Stok produk tidak mencukupi. Stok tersedia: ' . $variant->stock;
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }
                return back()->with('error', $message);
            }

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
            Log::error('Error adding to cart: ' . $e->getMessage(), ['exception' => $e]);
            $message = $e->getMessage(); // Tampilkan pesan error yang lebih spesifik dari service
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $message], 500);
            }
            return back()->with('error', $message);
        }
    }

    public function update(Request $request, $subdomain, $productCartId)
    {
        $validated = $request->validate(['quantity' => 'required|integer|min:1']);

        try {
            $isSuccess = $this->cartService->update($productCartId, $validated['quantity']);
            if (!$isSuccess) {
                // Pesan error spesifik akan dilempar dari service jika stok tidak cukup
                throw new Exception('Gagal memperbarui kuantitas.');
            }
            return response()->json([
                'success' => true,
                'message' => 'Kuantitas berhasil diperbarui.',
                'cart_count' => $this->cartService->getCartCount()
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function removeItems(Request $request)
    {
        $validated = $request->validate(['ids' => 'required|array', 'ids.*' => 'string']);
        try {
            $this->cartService->removeItems($validated['ids']);
            return response()->json([
                'success' => true,
                'message' => 'Item berhasil dihapus.',
                'cart_count' => $this->cartService->getCartCount()
            ]);
        } catch (Exception $e) {
            Log::error('Cart Deletion Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menghapus item.'], 500);
        }
    }
}
