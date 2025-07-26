<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product; // Jika dibutuhkan
use App\Models\Varian; // Jika dibutuhkan
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // Import Log untuk debugging
use Illuminate\Validation\Rule; // Import Rule for validation
use App\Exports\PendapatanExport; // Untuk pembayaran langganan (nama asli PendapatanExport)
use App\Exports\ProductOrdersExport; // Untuk pembayaran produk (nama baru)
use Maatwebsite\Excel\Facades\Excel;

class DashboardMitraController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $shop = $user->shop;

        if (!$shop) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini karena tidak memiliki toko.');
        }

        $shopId = $shop->id;

        // --- Perbarui: Filter metrik utama Dashboard untuk 30 hari terakhir (atau berdasarkan chart_range) ---
        $chartRange = $request->input('chart_range', '30_days'); // Default: 30 hari
        $startDate = null;
        $endDate = Carbon::now()->endOfDay(); // Untuk keseragaman, endDate selalu sekarang

        switch ($chartRange) {
            case '7_days':
                $startDate = Carbon::now()->subDays(6)->startOfDay();
                break;
            case '30_days':
                $startDate = Carbon::now()->subDays(29)->startOfDay();
                break;
            case 'this_month':
                $startDate = Carbon::now()->startOfMonth();
                break;
            case 'this_year':
                $startDate = Carbon::now()->startOfYear();
                break;
            default: // Fallback
                $startDate = Carbon::now()->subDays(29)->startOfDay();
                $chartRange = '30_days';
                break;
        }

        // Query dasar untuk metrik utama Dashboard (difilter berdasarkan rentang waktu)
        $mainMetricsQuery = Order::where('shop_id', $shopId)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate]);

        $totalRevenue = (clone $mainMetricsQuery)->sum('total_price') ?? 0;
        $totalOrders = (clone $mainMetricsQuery)->count();
        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        // Untuk keuntungan bersih, kita perlu me-load items.variant
        $completedOrdersForProfit = (clone $mainMetricsQuery)
            ->with(['items.variant'])
            ->get(); // Ambil semua untuk perhitungan profit

        $totalNetProfit = 0;
        foreach ($completedOrdersForProfit as $order) {
            foreach ($order->items as $item) {
                if ($item->variant && $item->variant->modal_price !== null) {
                    $profitPerItem = ($item->price - $item->variant->modal_price);
                    $totalNetProfit += ($profitPerItem * $item->quantity);
                } else {
                    Log::warning("Melewati perhitungan keuntungan untuk OrderItem ID: {$item->id}. Varian tidak ditemukan atau modal_price NULL.");
                }
            }
        }
        $totalNetProfit = max(0, $totalNetProfit);


        // Data untuk Grafik Pendapatan (query ini sudah benar untuk grafik)
        $salesData = Order::where('status', 'completed')
            ->where('created_at', '>=', $startDate)
            ->where('shop_id', $shopId)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_price) as revenue'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Tabel Produk Terlaris (tetap sama)
        $topSellingProducts = Product::where('shop_id', $shopId)
            ->withCount([
                'orderItems as sold_count' => function ($query) {
                    $query->select(DB::raw('SUM(quantity)'));
                }
            ])
            ->having('sold_count', '>', 10) // hanya produk dengan jumlah terjual > 10
            ->orderBy('sold_count', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard-mitra.dashboard', compact(
            'totalRevenue',
            'totalOrders',
            'averageOrderValue',
            'salesData',
            'topSellingProducts',
            'totalNetProfit',
            'chartRange'
        ));
    }

    /**
     * Menampilkan halaman detail transaksi (semua pesanan COMPLETED).
     */
    public function transactions(Request $request) // Tambahkan Request $request
    {
        $user = Auth::user();
        $shop = $user->shop;

        if (!$shop) {
            abort(403, 'Akses ditolak. Anda tidak memiliki toko yang terdaftar.');
        }

        $shopId = $shop->id;

        // --- Logika Filter ---
        $query = Order::where('shop_id', $shopId)
            ->where('status', Order::STATUS_COMPLETED) // Default hanya completed
            ->with([
                'user',          // Untuk nama pelanggan
                'items.product', // Untuk detail produk utama
                'items.variant', // Untuk harga modal dan nama varian
                'payment',       // Untuk status pembayaran
                'shipping',      // Untuk detail pengiriman (resi, layanan)
            ]);

        // 1. Filter Tanggal Order (Prioritas: date_range, lalu start_date/end_date kustom)
        $dateRange = $request->input('date_range', 'last_30_days'); // Default ke 30 hari terakhir
        $startDate = null;
        $endDate = null;

        switch ($dateRange) {
            case 'today':
                $startDate = Carbon::today();
                $endDate = Carbon::now()->endOfDay();
                break;
            case 'yesterday':
                $startDate = Carbon::yesterday();
                $endDate = Carbon::yesterday()->endOfDay();
                break;
            case 'last_7_days':
                $startDate = Carbon::now()->subDays(6)->startOfDay();
                $endDate = Carbon::now()->endOfDay();
                break;
            case 'this_month':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
            case 'last_30_days':
                $startDate = Carbon::now()->subDays(29)->startOfDay();
                $endDate = Carbon::now()->endOfDay();
                break;
            case 'this_year':
                $startDate = Carbon::now()->startOfYear();
                $endDate = Carbon::now()->endOfYear();
                break;
            case 'custom':
                // Jika custom, ambil dari input start_date dan end_date
                $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : null;
                $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : null;
                break;
            default: // Fallback to last_30_days if invalid range
                $startDate = Carbon::now()->subDays(29)->startOfDay();
                $endDate = Carbon::now()->endOfDay();
                $dateRange = 'last_30_days';
                break;
        }

        // Terapkan filter tanggal ke query
        if ($startDate) {
            $query->where('order_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('order_date', '<=', $endDate);
        }

        // 2. Filter Nama Pelanggan
        if ($request->filled('customer_name')) {
            $customerName = $request->input('customer_name');
            $query->whereHas('user', function ($q) use ($customerName) {
                $q->where('name', 'like', '%' . $customerName . '%');
            });
        }

        // 3. Filter Metode Pengiriman
        if ($request->filled('delivery_method')) {
            $query->where('delivery_method', $request->input('delivery_method'));
        }

        // 4. Filter Layanan Pengiriman (melalui relasi shipping)
        if ($request->filled('delivery_service')) {
            $deliveryService = $request->input('delivery_service');
            $query->whereHas('shipping', function ($q) use ($deliveryService) {
                $q->where('delivery_service', 'like', '%' . $deliveryService . '%');
            });
        }

        // 5. Pengurutan (default: terbaru)
        $sortBy = $request->input('sort_by', 'order_date');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);


        $completedOrders = $query->paginate(20)->withQueryString();

        // Hitung total pendapatan, total modal, dan keuntungan bersih secara keseluruhan (setelah filter dan pagination)
        $totalAllRevenue = 0;
        $totalAllCost = 0;
        $totalAllProfit = 0;

        foreach ($completedOrders as $order) {
            $orderCost = 0;
            foreach ($order->items as $item) {
                if ($item->variant && $item->variant->modal_price !== null) {
                    $itemCost = $item->variant->modal_price * $item->quantity;
                    $orderCost += $itemCost;
                } else {
                    Log::warning("Skipping cost calculation for OrderItem ID: {$item->id}. Variant or modal_price NULL/not found.");
                }
            }
            $order->calculated_cost = $orderCost;
            $order->calculated_profit = $order->total_price - $orderCost;

            $totalAllRevenue += $order->total_price;
            $totalAllCost += $orderCost;
            $totalAllProfit += $order->calculated_profit;
        }

        // Ambil daftar unik layanan pengiriman untuk dropdown filter
        $deliveryServices = DB::table('shippings')
            ->select('delivery_service')
            ->distinct()
            ->whereNotNull('delivery_service')
            ->orderBy('delivery_service')
            ->pluck('delivery_service')
            ->toArray();


        return view('dashboard-mitra.transactions.index', compact(
            'completedOrders',
            'totalAllRevenue',
            'totalAllCost',
            'totalAllProfit',
            'dateRange',    // Kirim kembali pilihan rentang tanggal
            'startDate',    // Kirim kembali untuk mengisi input custom
            'endDate',      // Kirim kembali untuk mengisi input custom
            'deliveryServices'
        ));
    }
    public function exportSubscriptionPaymentsExcel(Request $request) // Metode BARU untuk langganan
    {
        $user = Auth::user();
        $shopId = optional($user->shop)->id; // Shop ID jika mitra, null jika admin umum

        // Filter parameters (passed directly to export class)
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $isGeneralAdminReport = !Auth::user()->hasRole('mitra'); // Asumsi admin umum tidak punya shopId

        $fileName = 'laporan_langganan_' . ($shopId ? Str::slug(optional($user->shop)->shop_name) : 'admin') . '_' . Carbon::now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new PendapatanExport($startDate, $endDate, $shopId, $isGeneralAdminReport), $fileName);
    }

    /**
     * Mengekspor data pembayaran pesanan produk ke Excel.
     * Ini adalah export untuk Mitra (pesanan produknya) atau Admin Umum.
     */
    public function exportProductOrdersExcel(Request $request) // Metode BARU untuk produk
    {
        $user = Auth::user();
        $shopId = optional($user->shop)->id;

        $isGeneralAdminReport = !Auth::user()->hasRole('mitra');

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $customerName = $request->input('customer_name');
        $deliveryMethod = $request->input('delivery_method');
        $deliveryService = $request->input('delivery_service');

        // Note: Filters beyond date range are handled in the export class's collection method.
        // For simplicity, we just pass basic date range and shopId.
        // If complex filters like customer_name are needed, they should be passed to the constructor.
        // For now, let's keep it simple as date range and shop.

        $fileName = 'laporan_produk_terjual_' . ($shopId ? Str::slug(optional($user->shop)->shop_name) : 'admin') . '_' . Carbon::now()->format('Ymd_His') . '.xlsx';

        // Panggil ProductOrdersExport
        return Excel::download(new ProductOrdersExport($startDate, $endDate, $shopId, $isGeneralAdminReport), $fileName);
    }
}
