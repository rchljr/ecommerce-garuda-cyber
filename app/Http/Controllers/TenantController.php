<?php

namespace App\Http\Controllers;

use App\Models\Hero;
use App\Models\Banner;
use App\Models\Tenant;
use App\Models\Product;
use App\Models\Template;
use App\Models\Subdomain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class TenantController extends Controller
{
    /**
     * Menampilkan halaman utama (homepage) dari sebuah tenant berdasarkan subdomain.
     *
     * @param string $subdomain Nama subdomain yang diakses dari URL.
     * @return \Illuminate\View\View
     */
    public function showHomepage($subdomain)
    {
        $tenant = Tenant::whereHas('subdomain', function ($query) use ($subdomain) {
            $query->where('subdomain_name', $subdomain);
        })->with('template')->firstOrFail();

        // Aktifkan koneksi tenant
        $tenant->makeCurrent();

        $templatePath = $tenant->template->path;
        $heroes = Hero::where('is_active', true)->orderBy('order')->get();
        $banners = Banner::where('is_active', true)->orderBy('order')->get();
        $bestSellers = Product::where('is_best_seller', true)->where('status', 'active')->latest()->limit(8)->get();
        $newArrivals = Product::where('is_new_arrival', true)->where('status', 'active')->latest()->limit(8)->get();
        $hotSales = Product::where('is_hot_sale', true)->where('status', 'active')->latest()->limit(8)->get();

        // Kembalikan ke database pusat
        \Spatie\Multitenancy\Models\Tenant::forgetCurrent();

        return view($templatePath . '.home', compact(
            'tenant',
            'heroes',
            'banners',
            'bestSellers',
            'newArrivals',
            'hotSales'
        ));
    }
}
