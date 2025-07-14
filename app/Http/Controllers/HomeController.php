<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hero; 
use App\Models\Banner;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

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
        if (!$tenant) {
            abort(404, 'Tenant tidak ditemukan.');
        }

        // ## PERBAIKAN BARU ##
        // Ambil user (mitra) dari tenant terlebih dahulu
        $user = $tenant->user;
        if (!$user) {
            abort(404, 'User (mitra) untuk tenant ini tidak ditemukan.');
        }

        // Ambil data 'shop' dari user (mitra)
        $shop = $user->shop;
        if (!$shop) {
            abort(404, 'Toko untuk mitra ini tidak ditemukan.');
        }
        
        $templatePath = $tenant->template->path;

        // 2. Ambil data yang dibutuhkan oleh template (logika Anda tidak berubah)
        $heroes = Hero::where('is_active', true)->orderBy('order')->get();
        $banners = Banner::where('is_active', true)->orderBy('order')->get();
        $bestSellers = Product::where('is_best_seller', true)->where('status', 'active')->latest()->limit(8)->get();
        $newArrivals = Product::where('is_new_arrival', true)->where('status', 'active')->latest()->limit(8)->get();
        $hotSales = Product::where('is_hot_sale', true)->where('status', 'active')->latest()->limit(8)->get();

        // 3. Tampilkan view dari template yang benar dengan SEMUA data yang dibutuhkan
        return view($templatePath . '.home', [
            'tenant' => $tenant,
            'shop' => $shop, // <-- Variabel $shop sekarang dikirim ke view
            'heroes' => $heroes,
            'banners' => $banners,
            'bestSellers' => $bestSellers,
            'newArrivals' => $newArrivals,
            'hotSales' => $hotSales,
        ]);
        
    }
}