<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardMitraController extends Controller
{
    public function index()
    {
        $user = Auth::user();
      
        $thirtyDaysAgo = Carbon::now()->subDays(30);

        // 1. Metrik Utama (dengan pengaman)
        $totalRevenue = Order::where('status', 'completed')->sum('total_price') ?? 0; // Menjadi 0 jika NULL
        $totalOrders = Order::where('status', 'completed')->count();

        // Pengecekan pembagian dengan nol
        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        // 2. Data untuk Grafik (Ini sudah aman, akan mengembalikan koleksi kosong)
        $salesData = Order::where('status', 'completed')
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_price) as revenue'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // 3. Tabel Produk (Ini juga sudah aman)
        $topSellingProducts = Product::withCount(['orderItems as sold_count' => function ($query) {
            $query->select(DB::raw('SUM(quantity)'));
        }])
            ->orderBy('sold_count', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard-mitra.dashboard', compact(
            'totalRevenue',
            'totalOrders',
            'averageOrderValue',
            'salesData',
            'topSellingProducts'
        ));
    }
}
