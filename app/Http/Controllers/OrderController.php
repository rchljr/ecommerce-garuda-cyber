<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Menampilkan daftar semua pesanan untuk toko user.
     */
    public function index()
    {
        $user = Auth::user();
        // Asumsi user punya relasi ke subdomain/toko
        $subdomain = $user->subdomain;

        // Ambil data order dengan relasinya (eager loading)
        $orders = Order::where('subdomain_id', $subdomain->id)
                       ->with(['user', 'items.product'])
                       ->latest()
                       ->paginate(15);

        // Kirim data ke view 'orders.index'
        return view('dashboard-mitra.orders.index', compact('orders'));
    }
}