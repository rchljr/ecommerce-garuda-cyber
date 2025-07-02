<?php

namespace App\Http\Controllers\Customer;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CustomerOrderController extends Controller
{
    /**
     * Menampilkan daftar pesanan milik pengguna yang sedang login.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $user = Auth::user();

        // Ambil semua pesanan dari pengguna i ni, dengan relasi ke produk
        $orders = Order::where('user_id', $user->id)
            ->with(['subdomain.user.shop', 'items.product'])
            ->when($search, function ($query, $search) {
                return $query->whereHas('subdomain', function ($q) use ($search) {
                    $q->where('subdomain_name', 'like', '%' . $search . '%');
                })->orWhereHas('items.product', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                });
            })
            ->latest()
            ->paginate(10);

        return view('customer.orders', compact('orders', 'search'));
    }
}
