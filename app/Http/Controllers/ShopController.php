<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    /**
     * Menampilkan halaman toko dengan filter dan sorting.
     */
    public function index(Request $request)
    {
        // Memulai query dasar untuk produk yang statusnya 'active'
        $query = Product::where('status', 'active');

        // 1. Filter berdasarkan Kategori
        // Jika ada parameter 'category' di URL, filter produk berdasarkan slug kategori
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // 2. Filter berdasarkan Rentang Harga
        // Jika ada parameter 'min_price' dan 'max_price'
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // 3. Mengurutkan (Sorting) Produk
        // Cek parameter 'sort' di URL
        if ($request->input('sort') == 'price_asc') {
            $query->orderBy('price', 'asc');
        } elseif ($request->input('sort') == 'price_desc') {
            $query->orderBy('price', 'desc');
        } else {
            // Urutan default adalah produk terbaru
            $query->latest(); 
        }

        // Ambil hasil akhir dengan paginasi (misalnya 9 produk per halaman)
        $products = $query->paginate(9)->withQueryString();

        // Ambil semua kategori untuk ditampilkan di sidebar
        $categories = Category::all();

        // Kirim semua data yang dibutuhkan ke view
        return view('template1.shop', [
            'products' => $products,
            'categories' => $categories
        ]);
    }
    
    /**
     * Menampilkan halaman detail untuk satu produk.
     * (Anda sudah memiliki ini, tetapi penting untuk disertakan)
     */
    public function show(Product $product)
    {
        // Muat relasi yang diperlukan
        $product->load('category', 'variants', 'gallery', 'tags');

        // Ambil produk terkait
        $relatedProducts = Product::where('category_id', $product->category_id)
                                ->where('id', '!=', $product->id)
                                ->limit(4)
                                ->get();
                                
        return view('template1.details', compact('product', 'relatedProducts'));
    }
}
