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
        $shop = $user->shop;

        if (!$shop) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini karena tidak memiliki toko.');
        }

        $orders = Order::where('shop_id', $shop->id)
            ->with(['user', 'items.product'])
            ->latest()
            ->paginate(15);

        return view('dashboard-mitra.orders.index', compact('orders'));
    }

    /**
     * Menampilkan detail pesanan tunggal.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\View\View
     */
    public function show(Order $order)
    {
        $user = Auth::user();
        $shop = $user->shop;

        if (!$shop || $order->shop_id !== $shop->id) {
            abort(403, 'Anda tidak diizinkan melihat pesanan ini.');
        }

        // Memuat relasi untuk ditampilkan di halaman detail
        $order->load(['user', 'items.product']);

        // Debugging: Periksa isi objek $order dan relasi 'items'
        // Jika $order->items kosong, ini akan menunjukkan masalah.
        //dd($order->toArray(), $order->items->toArray());

        // Debugging alternatif jika $order->items->toArray() error atau kosong
        // Ini akan langsung query database untuk order items berdasarkan order ID
        // dd($order->id, \App\Models\OrderItem::where('order_id', $order->id)->get()->toArray());


        return view('dashboard-mitra.orders.show', compact('order'));
    }
}
