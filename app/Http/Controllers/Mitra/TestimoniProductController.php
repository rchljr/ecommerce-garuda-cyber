<?php

namespace App\Http\Controllers\Mitra;

use App\Models\Testimoni;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TestimoniProductController extends Controller
{
    /**
     * Menampilkan daftar testimoni untuk produk milik mitra yang sedang login.
     */
    public function index()
    {
        $user = Auth::user();

        // Ambil semua ID produk yang dimiliki oleh mitra ini.
        // Asumsi ada relasi 'products' pada model User.
        // Sesuaikan jika nama relasinya berbeda, misalnya 'shop->products'.
        $productIds = $user->products()->pluck('id');

        // Ambil semua testimoni yang terkait dengan produk-produk tersebut.
        $testimonials = Testimoni::whereIn('product_id', $productIds)
                                ->with('product') // Eager load relasi produk untuk efisiensi
                                ->latest() // Urutkan dari yang terbaru
                                ->paginate(10); // Gunakan pagination

        return view('dashboard-mitra.testimoni', compact('testimonials'));
    }
}