<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product; // Pastikan Product diimport
use App\Models\Varian;  // Pastikan Varian diimport
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // Import Log untuk debugging

class DashboardMitraController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $shop = $user->shop;

        if (!$shop) {
            abort(403, 'Anda tidak memiliki toko yang terdaftar untuk mengakses dashboard ini.');
        }

        $shopId = $shop->id;
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
        // Membutuhkan order yang completed, dengan item dan VARIAN terkait (untuk modal_price)
        $completedOrders = Order::where('status', 'completed')
                                ->where('shop_id', $shopId)
                                ->with(['items.variant']) // PENTING: Eager load order items dan variannya
                                ->get();

        $totalNetProfit = 0;
        foreach ($completedOrders as $order) {
            foreach ($order->items as $item) {
                // Pastikan varian dan harga modalnya ada sebelum menghitung keuntungan
                // $item->price adalah harga jual saat order dibuat (sudah disimpan di OrderItem)
                // $item->variant->modal_price adalah harga modal dari varian saat ini
                if ($item->variant && $item->variant->modal_price !== null) {
                    $profitPerItem = ($item->price - $item->variant->modal_price);
                    $totalNetProfit += ($profitPerItem * $item->quantity);
                } else {
                    // Log peringatan jika ada item order tanpa varian atau modal_price
                    Log::warning("Melewati perhitungan keuntungan untuk OrderItem ID: {$item->id}. Varian tidak ditemukan atau modal_price NULL.");
                }
            }
        }
        $totalNetProfit = max(0, $totalNetProfit); // Pastikan keuntungan bersih tidak negatif


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
        $topSellingProducts = Product::where('shop_id', $shopId)
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
            'totalNetProfit'
        ));
    }
}