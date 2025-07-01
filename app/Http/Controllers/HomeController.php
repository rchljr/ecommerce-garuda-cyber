<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hero; // Import model Hero
use App\Models\Banner;
use App\Models\Product;

class HomeController extends Controller
{
    /**
     * Tampilkan aplikasi dashboard.
     *
     * @return \Illuminate\Contracts->Support->Renderable
     */
    public function index()
    {

        $heroes = Hero::where('is_active', true)->orderBy('order')->get();
        $banners = Banner::where('is_active', true)->orderBy('order')->get();

        $bestSellers = Product::where('is_best_seller', true)
            ->where('status', 'active')
            ->latest()
            ->limit(8) // Ambil 8 produk
            ->get();

        $newArrivals = Product::where('is_new_arrival', true)
            ->where('status', 'active')
            ->latest()
            ->limit(8)
            ->get();

        // Untuk Hot Sales, Anda bisa menggunakan flag atau diskon
        $hotSales = Product::where('is_hot_sale', true)
            ->where('status', 'active')
            ->latest()
            ->limit(8)
            ->get();

        return view('template1.home', compact('heroes', 'banners','bestSellers', 'newArrivals', 'hotSales'));

        // Ambil semua hero yang aktif, diurutkan berdasarkan order
    }
}
