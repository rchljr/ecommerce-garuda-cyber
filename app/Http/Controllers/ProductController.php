<?php

namespace App\Http\Controllers; // Disarankan di dalam folder Admin

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Tag;
use App\Models\ProductVariant;
use App\Models\ProductGallery;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Menampilkan daftar semua produk.
     */
    public function index()
    {
        $products = Product::latest()->paginate(10);
        return view('Dashboard-mitra.products.index', compact('products'));
    }

    /**
     * Menampilkan form untuk membuat produk baru.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('Dashboard-mitra.products.create', compact('categories'));
    }

    /**
     * Menyimpan produk baru ke database.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'short_description' => 'nullable|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'main_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'gallery_images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'variants' => 'required|array',
            'variants.*.color' => 'required|string|max:255',
            'variants.*.size' => 'required|string|max:255',
            'variants.*.stock' => 'required|integer|min:0',
            'tags' => 'nullable|string',
            'sku' => 'nullable|string|unique:products,sku',
        ]);

        DB::beginTransaction();
        try {
            $mainImagePath = $request->file('main_image')->store('products/main', 'public');

            $product = Product::create([
                'name' => $validatedData['name'],
                'slug' => Str::slug($validatedData['name']) . '-' . uniqid(),
                'short_description' => $validatedData['short_description'],
                'description' => $validatedData['description'],
                'price' => $validatedData['price'],
                'sku' => $validatedData['sku'],
                'category_id' => $validatedData['category_id'],
                'main_image' => $mainImagePath,
                'status' => 'active',
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
        $product->load(['category', 'tags', 'variants', 'gallery']);
        return view('Dashboard-mitra.products.show', compact('product'));
    }

    /**
     * Menampilkan form untuk mengedit produk.
     */
    public function edit(Product $product)
    {
        $product->load(['tags', 'variants', 'gallery']);
        $categories = Category::orderBy('name')->get();
        return view('Dashboard-mitra.products.edit', compact('product', 'categories'));
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
            'category_id' => 'required|exists:categories,id',
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // Nullable on update
            'gallery_images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'variants' => 'required|array',
            'variants.*.color' => 'required|string|max:255',
            'variants.*.size' => 'required|string|max:255',
            'variants.*.stock' => 'required|integer|min:0',
            'tags' => 'nullable|string',
            'sku' => ['nullable', 'string', Rule::unique('products')->ignore($product->id)],
        ]);

        DB::beginTransaction();
        try {
            $updateData = $validatedData;
            
            // Hapus 'variants' dan 'tags' dari data update utama karena akan ditangani terpisah
            unset($updateData['variants'], $updateData['tags']);

            // Handle update gambar utama
            if ($request->hasFile('main_image')) {
                // Hapus gambar lama
                if ($product->main_image) {
                    Storage::disk('public')->delete($product->main_image);
                }
                // Simpan gambar baru
                $updateData['main_image'] = $request->file('main_image')->store('products/main', 'public');
            }

            // Update produk utama
            $product->update($updateData);

            // Update tags
            if (!empty($validatedData['tags'])) {
                $tagsInput = explode(',', $validatedData['tags']);
                $tagIds = [];
                foreach ($tagsInput as $tagName) {
                    $tag = Tag::firstOrCreate(['name' => trim($tagName)], ['slug' => Str::slug(trim($tagName))]);
                    $tagIds[] = $tag->id;
                }
                $product->tags()->sync($tagIds);
            } else {
                $product->tags()->sync([]); // Hapus semua tag jika input kosong
            }
            
            // Update varian (hapus yang lama, buat yang baru untuk simplisitas)
            $product->variants()->delete();
            foreach ($validatedData['variants'] as $variantData) {
                $product->variants()->create($variantData);
            }

            // Tambah gambar galeri baru (penghapusan gambar galeri lama memerlukan UI terpisah)
            if ($request->hasFile('gallery_images')) {
                foreach ($request->file('gallery_images') as $galleryFile) {
                    $galleryPath = $galleryFile->store('products/gallery', 'public');
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
            // Hapus gambar utama dari storage
            if ($product->main_image) {
                Storage::disk('public')->delete($product->main_image);
            }

            // Hapus semua gambar galeri dari storage
            foreach ($product->gallery as $galleryImage) {
                Storage::disk('public')->delete($galleryImage->image_path);
            }

            // Hapus produk dari database (varian, galeri, dan relasi tag akan terhapus otomatis karena onDelete('cascade'))
            $product->delete();

            DB::commit();
            return redirect()->route('mitra.products.index')->with('success', 'Produk berhasil dihapus!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
