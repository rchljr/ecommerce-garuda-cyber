<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hero; // Import model Hero
use App\Models\Banner;

class HomeController extends Controller
{
    /**
     * Tampilkan aplikasi dashboard.
     *
     * @return \Illuminate\Contracts->Support->Renderable
     */
    public function index()
    {
        // Ambil semua hero yang aktif, diurutkan berdasarkan order
        $heroes = Hero::where('is_active', true)->orderBy('order')->get();
        $banners = Banner::where('is_active', true)->orderBy('order')->get();

        return view('template1.home', compact('heroes', 'banners'));
    }
}
