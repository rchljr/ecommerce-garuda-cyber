<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ShopController extends Controller
{
   
    /**
     * Menampilkan halaman toko dengan data yang sudah difilter untuk tenant tertentu.
     */
    public function index(Request $request)
    {
        //  dd(new \App\Models\Category);
        // 1. Ambil data tenant dari request (sudah disiapkan oleh middleware)
        $tenant = $request->get('tenant');
        if (!$tenant) {
            abort(404, 'Tenant tidak ditemukan dari middleware.');
        }

        $mitra = $tenant->user;
        if (!$mitra) {
            abort(404, 'User (mitra) tidak ditemukan pada tenant.');
        }

        $shop = $mitra->shop;
        if (!$shop) {
            abort(404, 'Data toko (shop) tidak ditemukan untuk mitra ini.');
        }

        // 2. Dapatkan kategori utama dari toko ini
        $mainCategorySlug = $shop->product_categories;

        // 3. Cari Kategori Utama
        $mainCategory = Category::where('slug', $mainCategorySlug)->first();

        // 4. Ambil hanya sub-kategori yang relevan untuk sidebar filter
        $sidebarCategories = $mainCategory ? $mainCategory->subcategories()->orderBy('name')->get() : collect();

        // 5. Query dasar HANYA untuk produk milik mitra ini
        $query = Product::where('user_id', $mitra->id)->where('status', 'active');

        // 6. Terapkan filter berdasarkan sub-kategori yang dipilih
        if ($request->filled('category')) {
            $query->whereHas('subCategory', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // 7. Terapkan filter harga
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // 8. Terapkan pengurutan
        if ($request->input('sort') == 'price_asc') {
            $query->orderBy('price', 'asc');
        } elseif ($request->input('sort') == 'price_desc') {
            $query->orderBy('price', 'desc');
        } else {
            $query->latest();
        }

        $products = $query->paginate(9)->withQueryString();

        // 9. Kirim data yang sudah difilter ke view yang benar
        return view($tenant->template->path . '.shop', [
            'tenant' => $tenant,
            'products' => $products,
            'categories' => $sidebarCategories,
        ]);
    }

    /**
     * Menampilkan halaman detail produk.
     */
    public function show(Request $request, $productSlug)
    {
        // --- PERBAIKAN DI SINI ---
        // 1. Cari produk HANYA berdasarkan slug, tanpa memperhatikan status.
        $product = Product::where('slug', $productSlug)->first();

        // 2. Periksa apakah produknya ada. Jika tidak, hentikan proses.
        if (!$product) {
            abort(404, "Produk dengan slug '{$productSlug}' tidak ditemukan.");
        }

        // 3. Jika produknya ada, periksa statusnya. Jika tidak aktif, hentikan.
        if ($product->status !== 'active') {
             abort(404, "Produk '{$product->name}' ditemukan, tetapi saat ini tidak tersedia.");
        }
        // --- AKHIR PERBAIKAN ---

        // Jika semua pengecekan lolos, baru lanjutkan.
        $tenant = $request->get('tenant');
        $templatePath = $tenant->template->path;

        // Baris ini sekarang aman karena $product dijamin tidak null.
        $product->load('subCategory.category', 'variants', 'gallery', 'tags');

        $relatedProducts = Product::where('sub_category_id', $product->sub_category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 'active')
            ->limit(4)
            ->get();

        return view($templatePath . '.details', compact('tenant', 'product', 'relatedProducts'));
    }
}
