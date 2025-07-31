<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Shop; // Pastikan model Shop diimpor
use App\Models\User;
use App\Models\Testimoni;// Pastikan model User diimpor jika digunakan
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // Import Log facade untuk debugging

class ShopController extends Controller
{
    /**
     * Menampilkan halaman toko dengan data yang sudah difilter untuk tenant tertentu.
     */
    public function index(Request $request)
    {
        try { // Tambahkan try-catch untuk menangkap error di controller ini
            $tenant = $request->get('tenant');
            if (!$tenant) {
                Log::error('ShopController@index: Tenant tidak ditemukan dari middleware.');
                abort(404, 'Tenant tidak ditemukan.');
            }

            $mitra = $tenant->user;
            if (!$mitra) {
                Log::error('ShopController@index: User (mitra) tidak ditemukan pada tenant.');
                abort(404, 'User (mitra) tidak ditemukan.');
            }

            $shop = $mitra->shop;
            if (!$shop) {
                Log::error('ShopController@index: Data toko (shop) tidak ditemukan untuk mitra ini.');
                abort(404, 'Data toko (shop) tidak ditemukan.');
            }

            $mainCategorySlug = $shop->product_categories;
            $mainCategory = Category::where('slug', $mainCategorySlug)->first();
            $sidebarCategories = $mainCategory ? $mainCategory->subcategories()->orderBy('name')->get() : collect();

            // Query dasar untuk produk milik toko ini
            // Eager load varian untuk harga, dan gambar jika akan ditampilkan di listing
            $query = Product::where('shop_id', $shop->id)
                ->where('status', 'active')
                ->with(['varians', 'gallery']); // Eager load gallery juga

            // Terapkan filter berdasarkan sub-kategori
            if ($request->filled('category')) {
                $query->whereHas('subCategory', function ($q) use ($request) {
                    $q->where('slug', $request->category);
                });
            }

            // Terapkan filter harga
            if ($request->filled('min_price')) {
                // Konversi harga ke float, menangani format rupiah jika ada
                $minPrice = (float) str_replace(['Rp', '.', ','], '', $request->min_price);
                $query->whereHas('varians', function ($q) use ($minPrice) {
                    $q->where('price', '>=', $minPrice);
                });
            }
            if ($request->filled('max_price')) {
                // Konversi harga ke float, menangani format rupiah jika ada
                $maxPrice = (float) str_replace(['Rp', '.', ','], '', $request->max_price);
                $query->whereHas('varians', function ($q) use ($maxPrice) {
                    $q->where('price', '<=', $maxPrice);
                });
            }

            // Terapkan pengurutan
            if ($request->input('sort') == 'price_asc' || $request->input('sort') == 'price_desc') {
                $sortDirection = ($request->input('sort') == 'price_asc') ? 'asc' : 'desc';

                $products = $query->select('products.*')
                    ->addSelect(DB::raw('(SELECT MIN(price) FROM varians WHERE varians.product_id = products.id) as min_variant_price'))
                    ->orderBy('min_variant_price', $sortDirection)
                    ->paginate(9)
                    ->withQueryString();
            } else {
                // Default sorting: terbaru
                $products = $query->latest()->paginate(9)->withQueryString();
            }

            // Tambahkan logging untuk produk yang dimuat
            Log::info('ShopController@index: Products loaded for shop ' . $shop->id, ['count' => $products->count()]);
            $products->each(function ($product) {
                Log::debug('Product: ' . $product->name, ['varians_count' => $product->varians->count(), 'gallery_count' => $product->gallery->count(), 'image_url' => $product->image_url]);
                $product->varians->each(function ($varian) {
                    Log::debug('  Varian: ' . $varian->name, ['options_data' => $varian->options_data, 'image_url' => $varian->image_url, 'price' => $varian->price, 'stock' => $varian->stock]);
                });
            });


            return view($tenant->template->path . '.shop', [
                'tenant' => $tenant,
                'shop' => $shop,
                'products' => $products,
                'categories' => $sidebarCategories,
            ]);

        } catch (\Throwable $e) {
            Log::error('ShopController@index: Fatal error rendering shop page.', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request_url' => $request->fullUrl()
            ]);
            abort(500, 'Terjadi kesalahan pada server. Silakan periksa log untuk detail.');
        }
    }

    /**
     * Menampilkan halaman detail produk.
     */
    public function show(Request $request, string $subdomain, string $productSlug)
    {
        try { // Tambahkan try-catch untuk menangkap error di controller ini
            $tenant = $request->get('tenant');
            $templatePath = $tenant->template->path;

            $shop = $tenant->user->shop;
            if (!$shop) {
                Log::error('ShopController@show: Toko tidak ditemukan untuk tenant ini. Subdomain: ' . $subdomain);
                abort(404, 'Toko tidak ditemukan untuk tenant ini.');
            }

            // Mencari produk berdasarkan slug DAN memastikan shop_id-nya cocok
            $product = Product::where('slug', $productSlug)
                ->where('shop_id', $shop->id)
                ->where('status', 'active')
                ->with(['varians', 'gallery', 'subCategory.category', 'tags']) // Load semua relasi yang dibutuhkan
                ->first();
            // dd($product);

            if (!$product) {
                Log::warning('ShopController@show: Produk tidak ditemukan atau tidak aktif. Slug: ' . $productSlug . ', Shop ID: ' . $shop->id);
                abort(404, 'Produk tidak ditemukan atau tidak aktif.');
            }

            // Log detail produk yang dimuat untuk halaman detail
            Log::info('ShopController@show: Product loaded for details page.', [
                'product_name' => $product->name,
                'product_image_url' => $product->image_url,
                'varians_count' => $product->varians->count(),
                'gallery_count' => $product->gallery->count()
            ]);
            $product->varians->each(function ($varian) {
                Log::debug('  Varian: ' . $varian->name, ['options_data' => $varian->options_data, 'image_url' => $varian->image_url, 'price' => $varian->price, 'stock' => $varian->stock]);
            });
            $product->gallery->each(function ($image) {
                Log::debug('  Gallery Image: ' . $image->image_url);
            });


            // Ambil produk terkait
            $relatedProducts = Product::where('shop_id', $shop->id)
                ->where('sub_category_id', $product->sub_category_id)
                ->where('id', '!=', $product->id)
                ->where('status', 'active')
                ->with('varians') // Eager load varian untuk produk terkait juga
                ->limit(4)
                ->get();

            $reviews = Testimoni::where('product_id', $product->id)
                ->where('status', 'published') // Hanya tampilkan yang sudah disetujui
                ->latest() // Urutkan dari yang terbaru
                ->get();

            // 3. HITUNG RATA-RATA RATING & JUMLAH ULASAN
            $averageRating = $reviews->avg('rating');
            $reviewCount = $reviews->count();

            return view($templatePath . '.details', compact('tenant', 'product', 'relatedProducts', 'reviews', 'averageRating', 'reviewCount'));

        } catch (\Throwable $e) {
            Log::error('ShopController@show: Fatal error rendering product details page.', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'product_slug' => $productSlug,
                'request_url' => $request->fullUrl()
            ]);
            abort(500, 'Terjadi kesalahan pada server. Silakan periksa log untuk detail.');
        }
    }
}