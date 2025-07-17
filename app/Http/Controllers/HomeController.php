<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;
// PENTING: Import model-model lama yang akan digunakan
use App\Models\Hero;
use App\Models\Banner;
use App\Models\Product;

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
        // 1. Ambil data tenant dari request
        $tenant = $request->get('tenant');
        $templatePath = $tenant->template->path;// 'template1'

        // 2. Ambil data shop yang terhubung ke tenant
        $shop = $tenant->shop;
        if (!$shop) {
            abort(404, 'Toko tidak ditemukan.');
        }

        // 3. Ambil data dari model-model lama, difilter berdasarkan shop_id
        // Pastikan model Hero dan Banner Anda memiliki kolom 'shop_id'
        $heroes = Hero::where('is_active', true)->orderBy('order')->get();
        $banners = Banner::where('is_active', true)->orderBy('order')->get();
        $bestSellers = Product::where('is_best_seller', true)->where('status', 'active')->latest()->limit(8)->get();
        $newArrivals = Product::where('is_new_arrival', true)->where('status', 'active')->latest()->limit(8)->get();
        $hotSales = Product::where('is_hot_sale', true)->where('status', 'active')->latest()->limit(8)->get();
        $products = $shop->products()->where('status', 'active')->latest()->get();

        // 5. Tampilkan view dengan semua data yang dibutuhkan oleh template versi lama
        return view($templatePath . '.home', [
            'shop' => $shop,
            'heroes' => $heroes,
            'banners' => $banners,
            'products' => $products,
            'bestSellers' => $bestSellers,
            'newArrivals' => $newArrivals,
            'hotSales' => $hotSales,
            'tenant' => $tenant,
            'subdomainName' => $subdomain, 
        ]);
    }
}