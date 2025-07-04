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
        // 1. Ambil data tenant dan path template dari middleware
        $tenant = $request->get('tenant');
        $templatePath = $tenant->template->path;

        // 2. Logika query produk Anda (tidak berubah)
        $query = Product::where('status', 'active');

        if ($request->filled('category')) {
            $query->whereHas('category', fn($q) => $q->where('slug', $request->category));
        }
        // ... sisa filter dan sort ...
        if ($request->input('sort') == 'price_asc') {
            $query->orderBy('price', 'asc');
        } elseif ($request->input('sort') == 'price_desc') {
            $query->orderBy('price', 'desc');
        } else {
            $query->latest();
        }

        $products = $query->paginate(9)->withQueryString();
        $categories = Category::all();

        // 3. Tampilkan view dari template yang benar
        return view($templatePath . '.shop', compact('tenant', 'products', 'categories'));
    }

    /**
     * Menampilkan halaman detail untuk satu produk.
     */
    public function show(Request $request, Product $product)
    {
        // 1. Ambil data tenant dan path template
        $tenant = $request->get('tenant');
        $templatePath = $tenant->template->path;

        // 2. Logika Anda untuk mengambil data terkait (tidak berubah)
        $product->load('category', 'variants', 'gallery', 'tags');
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit(4)
            ->get();

        // 3. Tampilkan view dari template yang benar
        return view($templatePath . '.details', compact('tenant', 'product', 'relatedProducts'));
    }
}
