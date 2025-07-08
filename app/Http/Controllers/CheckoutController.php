<?php
// File: app/Http/Controllers/CheckoutController.php (Baru)
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CartService;
// ... (use statements lain)

class CheckoutController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }
    public function index(Request $request)
    {
        // Validasi bahwa ada item yang dikirim dari keranjang
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*' => 'string', // ID bisa berupa string (dari session) atau integer
        ]);

        // Dapatkan item yang akan di-checkout menggunakan metode baru di service
        $checkoutItems = $this->cartService->getItemsByIds($request, $validated['items']);

        // Jika karena suatu alasan tidak ada item yang ditemukan (misal, item dari toko lain),
        // kembalikan ke keranjang dengan pesan error.
        if ($checkoutItems->isEmpty()) {
            return redirect()->route('tenant.cart.index', ['subdomain' => $request->route('subdomain')])
                ->with('error', 'Item yang dipilih tidak valid atau tidak tersedia.');
        }

        // Hitung subtotal dari item yang akan di-checkout
        $subtotal = $checkoutItems->sum(function ($item) {
            $product = is_object($item->product) ? $item->product : (object) ($item['product'] ?? []);
            $quantity = $item->quantity ?? ($item['quantity'] ?? 0);
            return ($product->price ?? 0) * $quantity;
        });

        // Kirim data ke view
        return view('customer.checkout', [
            'checkoutItems' => $checkoutItems,
            'subtotal' => $subtotal,
        ]);
    }

    public function process(Request $request)
    {
        // 1. Validasi semua input (alamat, shipping, voucher)
        // 2. Buat Order baru di database
        // 3. Buat OrderItems dari produk yang di-checkout
        // 4. Siapkan data untuk Midtrans
        // 5. Buat Snap Token
        // 6. Redirect ke halaman pembayaran atau kembalikan Snap Token
    }

    // Metode untuk mengambil data ongkir dari RajaOngkir
    public function getShippingCost(Request $request)
    {
        // Logika API call ke RajaOngkir
        // return response()->json(...);
    }
}
