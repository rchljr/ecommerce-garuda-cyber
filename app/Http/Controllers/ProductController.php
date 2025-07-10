<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Product; // Pastikan mengarah ke model tenant
use App\Models\Tag;     // Pastikan mengarah ke model tenant
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Menampilkan daftar semua produk.
     */
    public function index()
    {
        $user = Auth::user();
        $tenant = $user->tenant;
        $shop = $user->shop;

        if (!$tenant || !$shop || !$shop->product_categories) {
            abort(403, 'Profil toko atau tenant Anda belum lengkap.');
        }

        // Ambil kategori dari database PUSAT
        $mainCategory = Category::where('slug', $shop->product_categories)->first();
        $subCategoryIds = $mainCategory ? $mainCategory->subCategories()->pluck('id') : [];

        // Jalankan query produk di dalam database TENANT yang benar
        $products = $tenant->execute(function () use ($subCategoryIds) {
            return Product::whereIn('sub_category_id', $subCategoryIds)
                ->latest()
                ->paginate(10);
        });

        return view('dashboard-mitra.products.index', compact('products'));
    }

    /**
     * Menampilkan form untuk membuat produk baru.
     */
    public function create()
    {
        $user = Auth::user();
        $mainCategorySlug = optional($user->shop)->product_categories;

        // Gunakan nama variabel $subCategories
        $subCategories = collect();

        if ($mainCategorySlug) {
            $mainCategory = Category::where('slug', $mainCategorySlug)->first();
            if ($mainCategory) {
                $subCategories = $mainCategory->subCategories()->orderBy('name', 'asc')->get();
            }
        }

        // Kirim variabel $subCategories ke view
        return view('dashboard-mitra.products.create', compact('subCategories'));
    }

    /**
     * Menyimpan produk baru ke database tenant.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'short_description' => 'nullable|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sub_category_id' => 'required|exists:sub_categories,id',
            'main_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'gallery_images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'variants' => 'required|array',
            'variants.*.color' => 'required|string|max:255',
            'variants.*.size' => 'required|string|max:255',
            'variants.*.stock' => 'required|integer|min:0',
            'tags' => 'nullable|string',
            'sku' => 'nullable|string|unique:tenant.products,sku', // Validasi unique di koneksi tenant
            'is_best_seller' => 'nullable',
            'is_new_arrival' => 'nullable',
            'is_hot_sale' => 'nullable',
        ]);

        $user = Auth::user();
        $tenant = $user->tenant;

        if (!$tenant) {
            return back()->with('error', 'Tenant tidak ditemukan.');
        }

        // Jalankan semua operasi database di dalam database tenant
        $tenant->execute(function () use ($validatedData, $request, $user) {
            DB::transaction(function () use ($validatedData, $request, $user) {
                $mainImagePath = $request->file('main_image')->store('products/main', 'public');

                $product = Product::create([
                    'user_id' => $user->id,
                    'name' => $validatedData['name'],
                    'slug' => Str::slug($validatedData['name']) . '-' . uniqid(),
                    'short_description' => $validatedData['short_description'],
                    'description' => $validatedData['description'],
                    'price' => $validatedData['price'],
                    'sku' => $validatedData['sku'],
                    'sub_category_id' => $validatedData['sub_category_id'],
                    'main_image' => $mainImagePath,
                    'status' => 'active',
                    'is_best_seller' => $request->has('is_best_seller'),
                    'is_new_arrival' => $request->has('is_new_arrival'),
                    'is_hot_sale' => $request->has('is_hot_sale'),
                ]);

                if (!empty($validatedData['tags'])) {
                    $tagsInput = explode(',', $validatedData['tags']);
                    $tagIds = [];
                    foreach ($tagsInput as $tagName) {
                        $tag = Tag::firstOrCreate(['name' => trim($tagName)], ['slug' => Str::slug(trim($tagName))]);
                        $tagIds[] = $tag->id;
                    }
                    $product->tags()->sync($tagIds);
                }

                foreach ($validatedData['variants'] as $variantData) {
                    $product->variants()->create($variantData);
                }

                if ($request->hasFile('gallery_images')) {
                    foreach ($request->file('gallery_images') as $galleryFile) {
                        $galleryPath = $galleryFile->store('products/gallery', 'public');
                        $product->gallery()->create(['image_path' => $galleryPath]);
                    }
                }
            });
        });

        return redirect()->route('mitra.products.index')->with('success', 'Produk berhasil ditambahkan!');
    }

    /**
     * Menampilkan form untuk mengedit produk.
     * Kita tidak bisa menggunakan Route Model Binding di sini.
     */
    // app/Http/Controllers/ProductController.php

    public function edit($productId)
    {
        // ... (kode untuk mencari tenant dan produk tetap sama) ...
        $user = Auth::user();
        $tenant = $user->tenant;
        // ...

        $product = $tenant->execute(function () use ($productId) {
            return Product::with(['tags', 'variants', 'gallery'])->findOrFail($productId);
        });

        // Ambil sub-kategori dari database pusat
        $mainCategorySlug = optional($user->shop)->product_categories;

        // Gunakan nama variabel $subCategories
        $subCategories = collect();
        if ($mainCategorySlug) {
            $mainCategory = Category::where('slug', $mainCategorySlug)->first();
            if ($mainCategory) {
                $subCategories = $mainCategory->subCategories()->orderBy('name', 'asc')->get();
            }
        }

        // Kirim variabel $subCategories ke view
        return view('dashboard-mitra.products.edit', compact('product', 'subCategories'));
    }

    /**
     * Memperbarui produk di database tenant.
     */
    public function update(Request $request, $productId) // Terima ID sebagai string
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        if (!$tenant) {
            return back()->with('error', 'Tenant tidak ditemukan.');
        }

        // Jalankan semua operasi update di dalam database tenant
        $tenant->execute(function () use ($request, $productId) {
            $product = Product::findOrFail($productId);

            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'short_description' => 'nullable|string',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'sub_category_id' => 'required|exists:sub_categories,id',
                'main_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'gallery_images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'variants' => 'required|array',
                'variants.*.color' => 'required|string|max:255',
                'variants.*.size' => 'required|string|max:255',
                'variants.*.stock' => 'required|integer|min:0',
                'tags' => 'nullable|string',
                'sku' => ['nullable', 'string', Rule::unique('tenant.products')->ignore($product->id)],
                'is_best_seller' => 'nullable',
                'is_new_arrival' => 'nullable',
                'is_hot_sale' => 'nullable',
            ]);

            DB::transaction(function () use ($validatedData, $request, $product) {
                $updateData = $request->except(['_token', '_method', 'variants', 'tags', 'gallery_images']);
                $updateData['is_best_seller'] = $request->has('is_best_seller');
                $updateData['is_new_arrival'] = $request->has('is_new_arrival');
                $updateData['is_hot_sale'] = $request->has('is_hot_sale');

                if ($request->hasFile('main_image')) {
                    if ($product->main_image) {
                        Storage::disk('public')->delete($product->main_image);
                    }
                    $updateData['main_image'] = $request->file('main_image')->store('products/main', 'public');
                }

                $product->update($updateData);

                if (!empty($validatedData['tags'])) {
                    $tagsInput = explode(',', $validatedData['tags']);
                    $tagIds = [];
                    foreach ($tagsInput as $tagName) {
                        $tag = Tag::firstOrCreate(['name' => trim($tagName)], ['slug' => Str::slug(trim($tagName))]);
                        $tagIds[] = $tag->id;
                    }
                    $product->tags()->sync($tagIds);
                } else {
                    $product->tags()->sync([]);
                }

                $product->variants()->delete();
                foreach ($validatedData['variants'] as $variantData) {
                    $product->variants()->create($variantData);
                }

                if ($request->hasFile('gallery_images')) {
                    foreach ($request->file('gallery_images') as $galleryFile) {
                        $galleryPath = $galleryFile->store('products/gallery', 'public');
                        $product->gallery()->create(['image_path' => $galleryPath]);
                    }
                }
            });
        });

        return redirect()->route('mitra.products.index')->with('success', 'Produk berhasil diperbarui!');
    }

    /**
     * Menghapus produk dari database tenant.
     */
    public function destroy($productId) // Terima ID sebagai string
    {
        $tenant = Auth::user()->tenant;
        if (!$tenant) {
            return back()->with('error', 'Tenant tidak ditemukan.');
        }

        $tenant->execute(function () use ($productId) {
            $product = Product::findOrFail($productId);

            DB::transaction(function () use ($product) {
                if ($product->main_image) {
                    Storage::disk('public')->delete($product->main_image);
                }
                foreach ($product->gallery as $galleryImage) {
                    Storage::disk('public')->delete($galleryImage->image_path);
                }
                $product->delete();
            });
        });

        return redirect()->route('mitra.products.index')->with('success', 'Produk berhasil dihapus!');
    }
}
