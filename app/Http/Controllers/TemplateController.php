<?php

namespace App\Http\Controllers;

use App\Models\Hero;
use App\Models\Banner;
use App\Models\Product;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TemplateController extends Controller
{
    /**
     * Menampilkan preview dari sebuah template.
     *
     * @param  \App\Models\Template  $template
     * @return \Illuminate\View\View
     */
    public function preview(Template $template)
    {

        $heroes = Hero::where('is_active', true)->orderBy('order')->get();
        $banners = Banner::where('is_active', true)->orderBy('order')->get();

        $bestSellers = Product::where('is_best_seller', true)
            ->where('status', 'active')
            ->latest()
            ->limit(8)
            ->get();

        $newArrivals = Product::where('is_new_arrival', true)
            ->where('status', 'active')
            ->latest()
            ->limit(8)
            ->get();

        $hotSales = Product::where('is_hot_sale', true)
            ->where('status', 'active')
            ->latest()
            ->limit(8)
            ->get();

        $currentShop = null; // Inisialisasi dengan null sebagai default

        // Periksa apakah ada pengguna yang login
        if (Auth::check()) {
            $currentShop = Auth::user()->shop;
        }

        // Kirim semua data yang diperlukan ke tampilan
        return view($template->path . '.home', compact(
            'template',
            'heroes',
            'banners',
            'bestSellers',
            'newArrivals',
            'hotSales',
            'currentShop',
        ));
    }
}
