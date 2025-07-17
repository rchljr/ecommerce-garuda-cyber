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
use App\Models\SubCategory; // Make sure to import SubCategory model


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

        // Pastikan mitra memiliki toko yang terdaftar
        if (!$shop) {
            abort(403, 'Profil toko Anda belum lengkap.');
        }

        // Ambil produk yang HANYA memiliki shop_id yang sama dengan toko mitra.
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

        // Pastikan mitra memiliki toko dan kategori produk
        if (!$shop || !$shop->product_categories) {
            abort(403, 'Profil toko Anda belum lengkap untuk menambahkan produk.');
        }

        // Ambil sub-kategori berdasarkan kategori utama toko
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

        // Validasi keberadaan toko sebelum menyimpan
        if (!$shop) {
            return back()->with('error', 'Profil toko tidak ditemukan.');
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'short_description' => 'nullable|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sub_category_id' => 'required|exists:sub_categories,id', // Changed to sub_category_id
            'main_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'gallery_images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'variants' => 'required|array',
            'variants.*.color' => 'required|string|max:255',
            'variants.*.size' => 'required|string|max:255',
            'variants.*.stock' => 'required|integer|min:0',
            'tags' => 'nullable|string',
            'sku' => 'nullable|string|unique:products,sku',
            'is_best_seller' => 'nullable',
            'is_new_arrival' => 'nullable',
            'is_hot_sale' => 'nullable',
        ]);

        DB::beginTransaction();
        try {
            $mainImagePath = $this->uploadFile($request->file('main_image'), 'products/main');

            $product = Product::create([
                'user_id' => auth::id(),
                'shop_id' => $shop->id,
                'name' => $validatedData['name'],
                'slug' => Str::slug($validatedData['name']) . '-' . uniqid(),
                'short_description' => $validatedData['short_description'],
                'description' => $validatedData['description'],
                'price' => $validatedData['price'],
                'sku' => $validatedData['sku'],
                'sub_category_id' => $validatedData['sub_category_id'], // Changed to sub_category_id
                'main_image' => $mainImagePath,
                'status' => 'active', // Default status
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
                    // Gunakan Trait untuk upload file galeri
                    $galleryPath = $this->uploadFile($galleryFile, 'products/gallery');
                    $product->gallery()->create(['image_path' => $galleryPath]);
                }
            }

            DB::commit();
            return redirect()->route('mitra.products.index')->with('success', 'Produk berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
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

        $product->load(['tags', 'variants', 'gallery']);
        $user = Auth::user();
        $categories = collect(); // <-- PERBAIKAN: Nama variabel diubah

        $mainCategorySlug = optional($user->shop)->product_categories;

        if ($mainCategorySlug) {
            $mainCategory = Category::where('slug', $mainCategorySlug)->first();
            if ($mainCategory) {
                // <-- PERBAIKAN: Nama variabel diubah
                $categories = $mainCategory->subCategories()->orderBy('name', 'asc')->get();
            }
        }
        // <-- PERBAIKAN: compact() sekarang menggunakan 'categories'
        return view('dashboard-mitra.products.edit', compact('product', 'categories'));
    }

    /**
     * Memperbarui produk di database.
     */
    public function update(Request $request, Product $product)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'short_description' => 'nullable|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sub_category_id' => 'required|exists:sub_categories,id', // Changed to sub_category_id
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'gallery_images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'variants' => 'required|array',
            'variants.*.color' => 'required|string|max:255',
            'variants.*.size' => 'required|string|max:255',
            'variants.*.stock' => 'required|integer|min:0',
            'tags' => 'nullable|string',
            'sku' => ['nullable', 'string', Rule::unique('products')->ignore($product->id)],
            'is_best_seller' => 'nullable',
            'is_new_arrival' => 'nullable',
            'is_hot_sale' => 'nullable',
        ]);

        DB::beginTransaction();
        try {
            $updateData = $validatedData;

            unset($updateData['variants'], $updateData['tags'], $updateData['gallery_images']);

            // Add checkbox data
            $updateData['is_best_seller'] = $request->has('is_best_seller');
            $updateData['is_new_arrival'] = $request->has('is_new_arrival');
            $updateData['is_hot_sale'] = $request->has('is_hot_sale');

            if ($request->hasFile('main_image')) {
                // Gunakan Trait untuk hapus file lama
                $this->deleteFile($product->main_image);
                // Gunakan Trait untuk upload file baru
                $updateData['main_image'] = $this->uploadFile($request->file('main_image'), 'products/main');
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
                    // Gunakan Trait untuk upload file galeri
                    $galleryPath = $this->uploadFile($galleryFile, 'products/gallery');
                    $product->gallery()->create(['image_path' => $galleryPath]);
                }
            }


            DB::commit();
            return redirect()->route('mitra.products.index')->with('success', 'Produk berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus produk dari database.
     */
    public function destroy(Product $product)
    {
        DB::beginTransaction();
        try {
            // Gunakan Trait untuk hapus file utama
            $this->deleteFile($product->main_image);

            // Gunakan Trait untuk hapus file galeri
            foreach ($product->gallery as $galleryImage) {
                $this->deleteFile($galleryImage->image_path);
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
