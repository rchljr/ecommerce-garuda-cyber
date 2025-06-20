<?php

namespace App\Http\Controllers;

use App\Models\Slide; // Import model Slide
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Menampilkan halaman depan dengan slide aktif.
     */
    public function index()
    {
        // Ambil slide yang aktif dan urutkan berdasarkan 'order'
        $slides = Slide::where('is_active', true)->orderBy('order')->get();

        return view('homepage', compact('slides')); // Teruskan data slide ke view 'homepage.blade.php'
    }
}