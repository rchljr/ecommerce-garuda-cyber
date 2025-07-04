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
        // 1. Cari Tenant berdasarkan nama subdomain yang terkait.
        //    'subdomain' adalah nama relasi di model Tenant.
        //    'subdomain_name' adalah nama kolom di tabel subdomains.
        //    firstOrFail() akan otomatis menampilkan halaman 404 jika subdomain tidak ditemukan.
        $tenant = Tenant::whereHas('subdomain', function ($query) use ($subdomain) {
            $query->where('subdomain_name', $subdomain);
        })->with('template')->firstOrFail(); // Eager load relasi template

        // 2. Ambil path template dari data tenant yang ditemukan.
        $templatePath = $tenant->template->path; // contoh: 'template1'

        // 3. Ambil semua data yang dibutuhkan oleh template tersebut.
        //    (Logika ini sama seperti di TemplateController Anda)
        $heroes = Hero::where('is_active', true)->orderBy('order')->get();
        $banners = Banner::where('is_active', true)->orderBy('order')->get();
        $bestSellers = Product::where('is_best_seller', true)->where('status', 'active')->latest()->limit(8)->get();
        $newArrivals = Product::where('is_new_arrival', true)->where('status', 'active')->latest()->limit(8)->get();
        $hotSales = Product::where('is_hot_sale', true)->where('status', 'active')->latest()->limit(8)->get();

        // 4. Tampilkan view yang benar dengan semua data yang dibutuhkan.
        //    Laravel akan mencari file di: /resources/views/template1/home.blade.php
        return view($templatePath . '.home', compact(
            'tenant', // Kirim juga data tenant jika perlu
            'heroes',
            'banners',
            'bestSellers',
            'newArrivals',
            'hotSales'
        ));
    }
}
