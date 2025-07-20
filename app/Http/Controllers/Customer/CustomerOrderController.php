<?php

namespace App\Http\Controllers\Customer;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CustomerOrderController extends Controller
{
    /**
     * Menampilkan daftar pesanan milik pengguna yang sedang login dengan fitur pencarian.
     */
    public function index(Request $request, $subdomain)
    {
        $search = $request->input('search');
        $user = Auth::guard('customers')->user();

        // Ambil semua pesanan dari pengguna ini, dengan relasi yang diperlukan
        $orders = Order::where('user_id', $user->id)
            ->whereHas('subdomain.user.shop') // Hanya ambil order jika relasi ke toko masih lengkap
            ->whereHas('items.product')      // Hanya ambil order jika produknya masih ada
            // Eager load testimonials yang terkait dengan order
            ->with(['subdomain.user.shop', 'items.product', 'items.variant', 'shipping', 'voucher', 'testimonials'])
            ->when($search, function ($query, $search) {
                // Pencarian berdasarkan nama toko atau nama produk
                return $query->where(function ($q) use ($search) {
                    $q->whereHas('subdomain.user.shop', function ($subQuery) use ($search) {
                        $subQuery->where('shop_name', 'like', '%' . $search . '%');
                    })->orWhereHas('items.product', function ($subQuery) use ($search) {
                        $subQuery->where('name', 'like', '%' . $search . '%');
                    });
                });
            })
            ->latest() // Urutkan dari yang terbaru
            ->paginate(10);

        //  Kirimkan variabel $subdomain ke view
        return view('customer.orders', compact('orders', 'search', 'subdomain'));
    }
}
