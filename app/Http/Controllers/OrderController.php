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
        $subdomain = $user->subdomain;

        // Peningkatan: Pastikan user memiliki subdomain/toko
        if (!$subdomain) {
            // Jika tidak, tolak akses atau redirect
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        // Ambil data order dengan relasinya (eager loading)
        $orders = Order::where('subdomain_id', $subdomain->id)
            ->with(['user', 'items.product'])
            ->latest()
            ->paginate(15);

        // Kirim data ke view 'dashboard-mitra.orders.index'
        return view('dashboard-mitra.orders.index', compact('orders'));
    }

    /**
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\View\View
     */
    public function show(Order $order)
    {
        $user = Auth::user();
        $subdomain = $user->subdomain;

        // Peningkatan Keamanan: Pastikan order ini milik toko user yang sedang login
        if (!$subdomain || $order->subdomain_id !== $subdomain->id) {
            abort(403, 'Anda tidak diizinkan melihat pesanan ini.');
        }

        // Memuat relasi untuk ditampilkan di halaman detail
        $order->load(['user', 'items.product']);

        // Kirim data order ke view 'dashboard-mitra.orders.show'
        return view('dashboard-mitra.orders.show', compact('order'));
    }
}