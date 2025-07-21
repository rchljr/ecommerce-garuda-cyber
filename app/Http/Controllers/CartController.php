<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Product;
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

    public function add(Request $request, $subdomain)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'size' => 'required|string',
            'color' => 'required|string',
        ]);

        try {
            $this->cartService->add($request);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Produk berhasil ditambahkan!',
                    'cart_count' => $this->cartService->getCartCount(),
                ]);
            }
            return back()->with('success', 'Produk berhasil ditambahkan ke keranjang.');

        } catch (ModelNotFoundException $e) {
            $message = 'Varian produk yang dipilih tidak valid.';
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $message], 404);
            }
            return back()->with('error', $message);
        } catch (Exception $e) {
            Log::error('Error adding to cart: ' . $e->getMessage());
            $message = 'Gagal menambahkan produk ke keranjang.';
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
