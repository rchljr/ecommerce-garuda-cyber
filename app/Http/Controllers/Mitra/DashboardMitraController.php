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

        // Mengambil objek Shop dari user yang sedang login
        $shop = $user->shop;

        // Pastikan user memiliki toko (shop)
        if (!$shop) {
            // Jika tidak, tolak akses atau redirect
            abort(403, 'Anda tidak memiliki toko yang terdaftar untuk mengakses dashboard ini.');
        }

        $shopId = $shop->id; // ID toko (shop) mitra yang sedang login

        $thirtyDaysAgo = Carbon::now()->subDays(30);

        // 1. Metrik Utama
        $totalRevenue = Order::where('status', 'completed')
                             ->where('shop_id', $shopId)
                             ->sum('total_price') ?? 0;

        $totalOrders = Order::where('status', 'completed')
                            ->where('shop_id', $shopId)
                            ->count();

        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        // Hitung Keuntungan Bersih (Total Net Profit)
        // Membutuhkan order yang completed, dengan item dan produk terkait (untuk modal_price)
        $completedOrders = Order::where('status', 'completed')
                                ->where('shop_id', $shopId)
                                ->with(['items.product']) // Eager load order items dan produknya
                                ->get();

        $totalNetProfit = 0;
        foreach ($completedOrders as $order) {
            foreach ($order->items as $item) {
                // Pastikan produk dan harga modal ada sebelum menghitung keuntungan
                if ($item->product && $item->product->modal_price !== null) {
                    // Keuntungan per item = (harga jual saat order - harga modal produk) * kuantitas
                    // Asumsi $item->price adalah harga jual saat order dibuat
                    $profitPerItem = ($item->price - $item->product->modal_price);
                    $totalNetProfit += ($profitPerItem * $item->quantity);
                }
            }
        }
        // Pastikan keuntungan bersih tidak negatif (misalnya jika ada data modal_price yang hilang atau harga jual lebih rendah dari modal)
        $totalNetProfit = max(0, $totalNetProfit);


        // 2. Data untuk Grafik (difilter berdasarkan shop_id)
        $salesData = Order::where('status', 'completed')
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->where('shop_id', $shopId)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_price) as revenue'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // 3. Tabel Produk Terlaris (difilter berdasarkan shop_id)
        // Mengambil produk yang dimiliki oleh toko ini dan menghitung berapa banyak yang terjual
        $topSellingProducts = Product::where('shop_id', $shopId) // Filter langsung berdasarkan shop_id produk
            ->withCount([
                'orderItems as sold_count' => function ($query) {
                    $query->select(DB::raw('SUM(quantity)'));
                }
            ])
            ->orderBy('sold_count', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard-mitra.dashboard', compact(
            'totalRevenue',
            'totalOrders',
            'averageOrderValue',
            'salesData',
            'topSellingProducts',
            'totalNetProfit' // Meneruskan metrik baru ke view
        ));
    }
}
