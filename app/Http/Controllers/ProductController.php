<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Product;
use App\Models\Category;
use App\Traits\UploadFile;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\SubCategory;


class ProductController extends Controller
{
    use UploadFile;

    /**
     * Menampilkan daftar semua produk.
     */
    public function index()
    {
        $user = Auth::user();
        $shop = $user->shop;

        if (!$shop) {
            abort(403, 'Profil toko Anda belum lengkap.');
        }

        $products = Product::where('shop_id', $shop->id)
            ->latest()
            ->paginate(10);

        return view('dashboard-mitra.products.index', compact('products'));
    }

    /**
     * Menampilkan form untuk membuat produk baru.
     */
    public function create()
    {
        $user = Auth::user();
        $shop = $user->shop;
        $subCategories = collect();

        if (!$shop || !$shop->product_categories) {
            abort(403, 'Profil toko Anda belum lengkap untuk menambahkan produk.');
        }

        $mainCategory = Category::where('slug', $shop->product_categories)->first();
        if ($mainCategory) {
            $subCategories = $mainCategory->subCategories()->orderBy('name', 'asc')->get();
        }

        return view('dashboard-mitra.products.create', compact('subCategories'));
    }

    /**
     * Menyimpan produk baru ke database.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $shop = $user->shop;

        if (!$shop) {
            return back()->with('error', 'Profil toko tidak ditemukan.');
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            // Hapus validasi modal_price dan profit_percentage dari sini (sudah di level varian)
            // 'modal_price' => 'required|numeric|min:0',
            // 'profit_percentage' => 'required|numeric|min:0|max:100',
            'sub_category_id' => 'required|exists:sub_categories,id',
            'main_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'tags' => 'nullable|string',
            'sku' => 'nullable|string|unique:products,sku',
            'is_best_seller' => 'nullable',
            'is_new_arrival' => 'nullable',
            'is_hot_sale' => 'nullable',

            // --- Validasi untuk VARIAN PRODUK ---
            'variants' => 'required|array',
            'variants.*.name' => 'nullable|string|max:255',
            'variants.*.modal_price' => 'required|numeric|min:0', // BARU: Validasi modal_price per varian
            'variants.*.profit_percentage' => 'required|numeric|min:0|max:100', // BARU: Validasi profit_percentage per varian
            'variants.*.stock' => 'required|integer|min:0',
            'variants.*.options' => 'required|json',
        ]);

        DB::beginTransaction();

        try {
            $mainImagePath = null;
            if ($request->hasFile('main_image')) {
                $mainImagePath = $request->file('main_image')->store('products/main', 'public');
            }

            $galleryImagePaths = [];
            if ($request->hasFile('gallery_images')) {
                foreach ($request->file('gallery_images') as $file) {
                    if ($file->isValid()) {
                        $galleryImagePaths[] = $file->store('products/gallery', 'public');
                    }
                }
            }

            $product = Product::create([
                'user_id' => Auth::id(),
                'shop_id' => $shop->id,
                'name' => $validatedData['name'],
                'slug' => Str::slug($validatedData['name']) . '-' . uniqid(),
                'short_description' => $validatedData['short_description'],
                'description' => $validatedData['description'],
                // Hapus 'modal_price' dan 'profit_percentage' dari sini (sudah di level varian)
                'sku' => $validatedData['sku'] ?? null,
                'sub_category_id' => $validatedData['sub_category_id'],
                'main_image' => $mainImagePath,
                'gallery_image_paths' => $galleryImagePaths,
                'status' => 'active',
                'is_best_seller' => $request->boolean('is_best_seller'),
                'is_new_arrival' => $request->boolean('is_new_arrival'),
                'is_hot_sale' => $request->boolean('is_hot_sale'),
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
                $optionsData = json_decode($variantData['options'], true);

                $product->varians()->create([
                    'name' => $variantData['name'] ?? null,
                    'modal_price' => $variantData['modal_price'],
                    'profit_percentage' => $variantData['profit_percentage'],
                    'price' => $variantData['modal_price'] * (1 + ($variantData['profit_percentage'] / 100)), // Mengisi kolom 'price' dengan harga jual yang dihitung
                    'stock' => $variantData['stock'],
                    'options_data' => $optionsData,
                    'size' => null, // Set null jika kolom masih ada di DB tapi tidak digunakan
                    'color' => null, // Set null jika kolom masih ada di DB tapi tidak digunakan
                    'description' => null,
                    'status' => 'active',
                    'image_path' => null, // Jika ada gambar per varian, perlu logika upload di sini
                ]);
            }

            DB::commit();

            return redirect()->route('mitra.products.index')->with('success', 'Produk berhasil ditambahkan!');

        } catch (\Exception $e) {
            DB::rollBack();

            if (isset($mainImagePath) && Storage::disk('public')->exists($mainImagePath)) {
                Storage::disk('public')->delete($mainImagePath);
            }
            foreach ($galleryImagePaths as $path) {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }

            Log::error("Error saving product: " . $e->getMessage(), ['exception' => $e]);

            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan produk: ' . $e->getMessage());
        }
    }


    /**
     * Menampilkan detail produk tertentu.
     */
    public function show(Product $product)
    {
        if ($product->shop_id !== Auth::user()->shop->id) {
            abort(403, 'Anda tidak memiliki izin untuk melihat produk ini.');
        }

        // Pastikan varian dimuat untuk perhitungan harga di Product Model Accessor
        $product->load('varians');

        return view('dashboard-mitra.products.show', compact('product'));
    }

    /**
     * Menampilkan form untuk mengedit produk.
     */
    public function edit(Product $product)
    {
        if ($product->shop_id !== Auth::user()->shop->id) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit produk ini.');
        }

        $product->load(['tags', 'varians', 'gallery']); // Load 'varians' bukan 'variants'
        $user = Auth::user();
        $subCategories = collect();

        $mainCategorySlug = optional($user->shop)->product_categories;

        if ($mainCategorySlug) {
            $mainCategory = Category::where('slug', $mainCategorySlug)->first();
            if ($mainCategory) {
                $subCategories = $mainCategory->subCategories()->orderBy('name', 'asc')->get();
            }
        }

        // Untuk mengisi Alpine.js di frontend dengan data varian yang ada:
        // Anda perlu meneruskan $product->varians->toArray() atau memprosesnya
        // agar cocok dengan struktur `options` di Alpine.js productVariantsHandler.
        // Contoh:
        $existingVariantsForAlpine = $product->varians->map(function ($varian) {
            return [
                'id' => $varian->id, // Jika Anda ingin mengupdate varian yang ada
                'name' => $varian->name,
                'modal_price' => $varian->modal_price,
                'profit_percentage' => $varian->profit_percentage,
                'stock' => $varian->stock,
                'options' => json_encode($varian->options_data), // Encode kembali ke JSON string
            ];
        })->toArray();


        // Untuk tags, pastikan formatnya string koma-separated
        $existingTags = $product->tags->pluck('name')->implode(',');


        return view('dashboard-mitra.products.edit', compact('product', 'subCategories', 'existingVariantsForAlpine', 'existingTags'));
    }

    /**
     * Memperbarui produk di database.
     */
    public function update(Request $request, Product $product)
    {
        if ($product->shop_id !== Auth::user()->shop->id) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit produk ini.');
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            // HAPUS validasi modal_price dan profit_percentage dari sini (sudah di level varian)
            // 'modal_price' => 'required|numeric|min:0',
            // 'profit_percentage' => 'required|numeric|min:0|max:100',
            'sub_category_id' => 'required|exists:sub_categories,id',
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // Nullable karena mungkin tidak diubah
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // Nullable karena mungkin tidak diubah
            'tags' => 'nullable|string',
            'sku' => ['nullable', 'string', Rule::unique('products')->ignore($product->id)],
            'is_best_seller' => 'nullable',
            'is_new_arrival' => 'nullable',
            'is_hot_sale' => 'nullable',

            // --- Validasi untuk VARIAN PRODUK ---
            'variants' => 'required|array',
            'variants.*.name' => 'nullable|string|max:255',
            'variants.*.modal_price' => 'required|numeric|min:0', // BARU: Validasi modal_price per varian
            'variants.*.profit_percentage' => 'required|numeric|min:0|max:100', // BARU: Validasi profit_percentage per varian
            'variants.*.stock' => 'required|integer|min:0',
            'variants.*.options' => 'required|json',
            'variants.*.id' => 'nullable|exists:varians,id', // Jika Anda mengirim ID varian untuk update/delete selektif
        ]);

        DB::beginTransaction();
        try {
            $updateData = $validatedData;
            // Hapus 'variants' dan 'tags' dari updateData untuk Product::update
            unset($updateData['variants'], $updateData['tags']);
            // HAPUS INI JUGA jika 'modal_price' dan 'profit_percentage' tidak lagi di tabel products
            unset($updateData['modal_price'], $updateData['profit_percentage']); // TAMBAHKAN INI

            // Add checkbox data
            $updateData['is_best_seller'] = $request->boolean('is_best_seller');
            $updateData['is_new_arrival'] = $request->boolean('is_new_arrival');
            $updateData['is_hot_sale'] = $request->boolean('is_hot_sale');

            if ($request->hasFile('main_image')) {
                $this->deleteFile($product->main_image);
                $updateData['main_image'] = $this->uploadFile($request->file('main_image'), 'products/main');
            }

            $product->update($updateData);

            // --- Sinkronkan Tags ---
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

            // --- Sinkronkan Varian Produk ---
            $product->varians()->delete(); // Hapus semua varian lama
            foreach ($validatedData['variants'] as $variantData) {
                $optionsData = json_decode($variantData['options'], true);

                $product->varians()->create([
                    'name' => $variantData['name'] ?? null,
                    'modal_price' => $variantData['modal_price'],
                    'profit_percentage' => $variantData['profit_percentage'],
                    'price' => $variantData['modal_price'] * (1 + ($variantData['profit_percentage'] / 100)), // Harga jual dihitung
                    'stock' => $variantData['stock'],
                    'options_data' => $optionsData,
                    'size' => null, // Set null
                    'color' => null, // Set null
                    'description' => null,
                    'status' => 'active',
                    'image_path' => null,
                ]);
            }

            // --- Sinkronkan Galeri Gambar ---
            if ($request->hasFile('gallery_images')) {
                foreach ($product->gallery as $galleryImage) {
                    $this->deleteFile($galleryImage->image_path);
                    $galleryImage->delete();
                }
                foreach ($request->file('gallery_images') as $galleryFile) {
                    if ($galleryFile->isValid()) {
                        $galleryPath = $galleryFile->store('products/gallery', 'public');
                        $product->gallery()->create(['image_path' => $galleryPath]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('mitra.products.index')->with('success', 'Produk berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error updating product: " . $e->getMessage(), ['exception' => $e]);
            return back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui produk: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus produk dari database.
     */
    public function destroy(Product $product)
    {
        if ($product->shop_id !== Auth::user()->shop->id) {
            abort(403, 'Anda tidak memiliki izin untuk menghapus produk ini.');
        }

        DB::beginTransaction();
        try {
            $this->deleteFile($product->main_image);

            foreach ($product->gallery as $galleryImage) {
                $this->deleteFile($galleryImage->image_path);
                $galleryImage->delete();
            }

            $product->delete();

            DB::commit();
            return redirect()->route('mitra.products.index')->with('success', 'Produk berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}