<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;
use App\Models\Hero;
use App\Models\Banner;
use App\Models\Product;
use App\Models\Varian;
use Illuminate\Support\Facades\Log;
use Throwable;

class HomeController extends Controller
{
    /**
     * Menampilkan halaman utama (homepage) dari sebuah tenant.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request, $subdomain)
    {
        try {
            // 1. Ambil data tenant dari request (didapat dari middleware)
            $tenant = $request->get('tenant');
            if (!$tenant || !$tenant->template || !$tenant->user_id) {
                Log::error("Data tenant tidak lengkap (user_id/template) untuk subdomain: {$subdomain}");
                abort(404, 'Data konfigurasi toko tidak valid.');
            }
            $templatePath = $tenant->template->path;

            // 2. Ambil data shop berdasarkan user_id dari tenant.
            $shop = Shop::where('user_id', $tenant->user_id)->first();
            if (!$shop) {
                Log::error("Toko tidak ditemukan untuk tenant dengan user_id: {$tenant->user_id}");
                abort(404, 'Toko tidak ditemukan.');
            }

            // ====================================================================
            // Ambil data berdasarkan shop_id
            // ====================================================================
            $shopId = $shop->id;

            // 3. Ambil data Heroes berdasarkan shop_id
            $heroes = Hero::where('shop_id', $shopId)
                ->where('is_active', true)
                ->orderBy('order')
                ->get();

            // 4. Ambil data Banners berdasarkan shop_id
            $banners = Banner::where('shop_id', $shopId)
                ->where('is_active', true)
                ->orderBy('order')
                ->get();

            // 5. Buat query dasar untuk produk berdasarkan shop_id
            // PENTING: Eager load 'variants' di sini (SUDAH DIPERBAIKI)
           $baseProductQuery = Product::where('shop_id', $shopId)
                ->where('status', 'active')
                ->with('varians'); // *** PERBAIKAN DI SINI: 'varians' -> 'variants' ***

            // 6. Ambil koleksi produk yang berbeda dari query dasar
            $bestSellers = (clone $baseProductQuery)->where('is_best_seller', true)->latest()->limit(8)->get();
            $newArrivals = (clone $baseProductQuery)->where('is_new_arrival', true)->latest()->limit(8)->get();
            $hotSales = (clone $baseProductQuery)->where('is_hot_sale', true)->latest()->limit(8)->get();
            $allProducts = $bestSellers->merge($newArrivals)->merge($hotSales)->unique('id');
            // dd($allProducts);

            // 7. Tampilkan view dengan semua data yang dibutuhkan
            return view($templatePath . '.home', [
                'shop' => $shop,
                'heroes' => $heroes,
                'banners' => $banners,
                'bestSellers' => $bestSellers,
                'newArrivals' => $newArrivals,
                'hotSales' => $hotSales,
                'tenant' => $tenant,
                'subdomainName' => $subdomain,
                'allProducts' => $bestSellers->merge($newArrivals)->merge($hotSales)->unique('id'),
            ]);

        } catch (Throwable $e) {
            Log::error('====================================================================');
            Log::error('TERJADI FATAL ERROR SAAT MENCOBA MERENDER VIEW HOMEPAGE');
            Log::error('Subdomain: ' . $subdomain);
            Log::error('Error Message: ' . $e->getMessage());
            Log::error('File: ' . $e->getFile());
            Log::error('Line: ' . $e->getLine());
            Log::error('Stack Trace: ' . $e->getTraceAsString());
            Log::error('====================================================================');

            abort(500, 'Terjadi kesalahan pada server. Silakan periksa log untuk detail.');
        }
    }

}