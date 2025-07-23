<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Import DB facade

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
        // PENTING: Eager load varian untuk accessor harga
        $query = Product::where('shop_id', $shop->id)
                        ->where('status', 'active')
                        ->with('varians'); // Eager load varian di sini

        // 6. Terapkan filter berdasarkan sub-kategori yang dipilih
        if ($request->filled('category')) {
            $query->whereHas('subCategory', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // 7. Terapkan filter harga
        if ($request->filled('min_price')) {
            $minPrice = preg_replace('/[^0-9]/', '', $request->min_price);
            // Filter produk yang memiliki setidaknya satu varian dengan harga >= minPrice
            $query->whereHas('varians', function ($q) use ($minPrice) {
                $q->where('price', '>=', $minPrice); // Menggunakan kolom 'price' di tabel varian
            });
        }
        if ($request->filled('max_price')) {
            $maxPrice = preg_replace('/[^0-9]/', '', $request->max_price);
            // Filter produk yang memiliki setidaknya satu varian dengan harga <= maxPrice
            $query->whereHas('varians', function ($q) use ($maxPrice) {
                $q->where('price', '<=', $maxPrice); // Menggunakan kolom 'price' di tabel varian
            });
        }

        // 8. Terapkan pengurutan
        // Untuk mengurutkan berdasarkan harga varian terendah, kita perlu join dan group
        if ($request->input('sort') == 'price_asc' || $request->input('sort') == 'price_desc') {
            $sortDirection = ($request->input('sort') == 'price_asc') ? 'asc' : 'desc';

            // Join dengan tabel varians untuk mendapatkan harga varian terendah
            // Gunakan subquery untuk mendapatkan min_price per produk, lalu join
            $products = $query->with(['varians' => function($q) {
                        $q->orderBy('price', 'asc'); // Urutkan varian berdasarkan harga untuk memastikan min() bekerja
                    }])
                    ->select('products.*') // Pilih semua kolom produk
                    ->addSelect(DB::raw('(SELECT MIN(price) FROM varians WHERE varians.product_id = products.id) as min_variant_price'))
                    ->orderBy('min_variant_price', $sortDirection)
                    ->paginate(9)
                    ->withQueryString();

        } else {
            // Default sorting: terbaru
            $products = $query->latest()->paginate(9)->withQueryString();
        }


        // 9. Kirim data yang sudah difilter ke view yang benar
        return view($tenant->template->path . '.shop', [
            'tenant' => $tenant,
            'shop' => $shop,
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
        $product->load('subCategory.category', 'varians', 'gallery', 'tags'); // Pastikan 'varians' bukan 'variants'

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