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
        $query = Product::where('shop_id', $shop->id)->where('status', 'active');


        // 6. Terapkan filter berdasarkan sub-kategori yang dipilih
        if ($request->filled('category')) {
            $query->whereHas('subCategory', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // 7. Terapkan filter harga
        if ($request->filled('min_price')) {
            // Membersihkan nilai non-numerik dari input harga
            $minPrice = preg_replace('/[^0-9]/', '', $request->min_price);
            $query->where('price', '>=', $minPrice);
        }
        if ($request->filled('max_price')) {
            // Membersihkan nilai non-numerik dari input harga
            $maxPrice = preg_replace('/[^0-9]/', '', $request->max_price);
            $query->where('price', '<=', $maxPrice);
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
            'shop' => $shop, // <--- VARIABEL INI DITAMBAHKAN
            'products' => $products,
            'categories' => $sidebarCategories,
        ]);
    }

    /**
     * Menampilkan halaman detail produk.
     */
    public function show(Request $request, string $subdomain, string $productSlug)
    {
        $tenant = $request->get('tenant');
        $templatePath = $tenant->template->path;

        $shop = $tenant->user->shop;
        if (!$shop) {
            abort(404, 'Toko tidak ditemukan untuk tenant ini.');
        }

        // Mencari produk berdasarkan slug DAN memastikan shop_id-nya cocok
        $product = Product::where('slug', $productSlug)
            ->where('shop_id', $shop->id)
            ->where('status', 'active')
            ->first();

        if (!$product) {
            abort(404, 'Produk tidak ditemukan atau tidak aktif.');
        }

        // Load relasi
        $product->load('subCategory.category', 'variants', 'gallery', 'tags');

        // Ambil produk terkait
        $relatedProducts = Product::where('shop_id', $shop->id) // Pastikan produk terkait juga dari toko yang sama
            ->where('sub_category_id', $product->sub_category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 'active')
            ->limit(4)
            ->get();

        return view($templatePath . '.details', compact('tenant', 'product', 'relatedProducts'));
    }
}
