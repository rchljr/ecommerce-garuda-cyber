<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product; // Pastikan mengarah ke model tenant
use App\Models\Order;   // Pastikan mengarah ke model tenant
use Spatie\Multitenancy\Models\Tenant;
use Illuminate\Support\Facades\DB;

class DashboardMitraController extends Controller
{
    public function index()
    {
        // 1. Dapatkan user (mitra) yang sedang login
        $user = Auth::user();

        // 2. Dapatkan tenant milik user tersebut
        //    Ini mengasumsikan Anda punya relasi 'tenant' di model User
        $tenant = $user->tenant;

        // 3. Jika user tidak punya tenant, tampilkan halaman dengan data kosong
        if (! $tenant) {
            return view('mitra.dashboard', [
                'totalRevenue' => 0,
                'totalOrders' => 0,
                'topSellingProducts' => [],
            ]);
        }

        // 4. Jadikan tenant ini sebagai tenant yang "current" untuk sementara
        $tenant->makeCurrent();

        // 5. Sekarang, jalankan semua query.
        //    Koneksi database sudah otomatis berpindah ke database tenant.
        $totalRevenue = Order::where('status', 'completed')->sum('total_price');
        $totalOrders = Order::where('status', 'completed')->count();
        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
        $topSellingProducts = Product::query()
            ->select('products.*')
            ->selectSub(function ($query) {
                $query->from('order_items')
                    ->selectRaw('SUM(quantity)')
                    ->whereColumn('products.id', 'order_items.product_id');
            }, 'sold_count')
            ->orderByDesc('sold_count')
            ->limit(5) // Ambil 5 saja untuk dashboard
            ->get();
        $salesData = Order::selectRaw('MONTH(created_at) as month, SUM(total_price) as total')
            ->where('status', 'completed')
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy('month')
            ->get();
        $salesChartLabels = [];
        $salesChartValues = [];

        foreach ($salesData as $row) {
            $salesChartLabels[] = \Carbon\Carbon::create()->month($row->month)->format('F');
            $salesChartValues[] = $row->total;
        }
        // 6. KEMBALIKAN KONEKSI ke database pusat setelah selesai.
        //    Ini sangat penting agar tidak "bocor" ke request lain.
        Tenant::forgetCurrent();

        // 7. Kirim data statistik ke view dashboard
        return view('dashboard-mitra.dashboard', [
            'totalRevenue' => $totalRevenue,
            'totalOrders' => $totalOrders,
            'averageOrderValue' => $averageOrderValue,
            'topSellingProducts' => $topSellingProducts,
            'salesData' => $salesData,
            'salesChartLabels' => $salesChartLabels,
            'salesChartValues' => $salesChartValues,
        ]);
    }
}
