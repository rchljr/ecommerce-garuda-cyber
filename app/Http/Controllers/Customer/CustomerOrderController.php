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

        // Ambil semua pesanan dari pengguna ini, dengan relasi ke produk
        // Kita akan menggunakan data dummy untuk sekarang, ini bisa dihubungkan ke model produk asli nanti
        $orders = Order::where('user_id', $user->id)
                    ->when($search, function ($query, $search) {
                        // Logika pencarian bisa ditambahkan di sini, misalnya berdasarkan ID order atau nama produk
                        // Untuk saat ini, kita lewati dulu
                    })
                    ->latest()
                    ->paginate(10);
                    
        return view('customer.orders', compact('orders', 'search'));
    }
}
