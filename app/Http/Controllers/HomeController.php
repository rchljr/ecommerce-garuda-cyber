<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
    public function index(Request $request)
    {
        // 1. Ambil data tenant dari request (sudah disiapkan oleh middleware)
        $tenant = $request->get('tenant');
        $templatePath = $tenant->template->path;

        // 2. Ambil data yang dibutuhkan oleh template (logika Anda tidak berubah)
        $heroes = Hero::where('is_active', true)->orderBy('order')->get();
        $banners = Banner::where('is_active', true)->orderBy('order')->get();
        $bestSellers = Product::where('is_best_seller', true)->where('status', 'active')->latest()->limit(8)->get();
        $newArrivals = Product::where('is_new_arrival', true)->where('status', 'active')->latest()->limit(8)->get();
        $hotSales = Product::where('is_hot_sale', true)->where('status', 'active')->latest()->limit(8)->get();

        // 3. Tampilkan view dari template yang benar dengan semua data
        return view($templatePath . '.home', compact('tenant', 'heroes', 'banners', 'bestSellers', 'newArrivals', 'hotSales'));
    }
}
