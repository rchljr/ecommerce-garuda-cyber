<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SubCategory; // Make sure to import SubCategory model
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Menampilkan daftar semua produk.
     */
    public function index()
    {
        // Mengambil produk berdasarkan user yang login jika perlu, atau semua produk jika admin global
        $products = Product::latest()->paginate(10);
        return view('dashboard-mitra.products.index', compact('products'));
    }

    /**
     * Menampilkan form untuk membuat produk baru.
     */
    public function create()
    {
        $user = Auth::user();
        $subCategories = collect(); // Initialize as an empty collection

        // Assuming 'shop' relationship exists on the User model
        // and 'product_categories' stores the slug of the main category
        $mainCategorySlug = optional($user->shop)->product_categories;

        if ($mainCategorySlug) {
            $mainCategory = Category::where('slug', $mainCategorySlug)->first();
            if ($mainCategory) {
                // Assuming Category model has a hasMany relationship to SubCategory model named 'subCategories'
                $subCategories = $mainCategory->subCategories()->orderBy('name', 'asc')->get();
            }
        }

        return view('dashboard-mitra.products.create', compact('subCategories'));
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
            $mainImagePath = $request->file('main_image')->store('products/main', 'public');

            $product = Product::create([
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
        $product->load(['subCategory', 'tags', 'variants', 'gallery']); // Changed 'category' to 'subCategory'
        return view('dashboard-mitra.products.show', compact('product'));
    }

    /**
     * Menampilkan form untuk mengedit produk.
     */
    public function edit(Product $product)
    {
        $product->load(['tags', 'variants', 'gallery']);
        $user = Auth::user();
        $subCategories = collect(); // Initialize as an empty collection

        $mainCategorySlug = optional($user->shop)->product_categories;

        if ($mainCategorySlug) {
            $mainCategory = Category::where('slug', $mainCategorySlug)->first();
            if ($mainCategory) {
                $subCategories = $mainCategory->subCategories()->orderBy('name', 'asc')->get();
            }
        }
        return view('dashboard-mitra.products.edit', compact('product', 'subCategories')); // Pass subCategories
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
            if ($product->main_image) {
                Storage::disk('public')->delete($product->main_image);
            }

            foreach ($product->gallery as $galleryImage) {
                Storage::disk('public')->delete($galleryImage->image_path);
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