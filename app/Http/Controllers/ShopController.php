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
        // 1. Ambil data tenant dan path template (sudah benar)
        $tenant = $request->get('tenant');
        $templatePath = $tenant->template->path;

        // 2. PERBAIKAN: Memuat relasi yang benar
        // Kita memuat 'subCategory' dan juga 'category' yang ada di dalamnya
        $product->load('subCategory.category', 'variants', 'gallery', 'tags');

        // 3. PERBAIKAN: Mencari produk terkait berdasarkan sub_category_id
        // Produk terkait adalah produk dalam sub-kategori yang sama
        $relatedProducts = Product::where('sub_category_id', $product->sub_category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 'active') // Tambahan: hanya tampilkan produk terkait yang aktif
            ->limit(4)
            ->get();

        // 4. Tampilkan view dari template yang benar (sudah benar)
        return view($templatePath . '.details', compact('tenant', 'product', 'relatedProducts'));
    }
}
