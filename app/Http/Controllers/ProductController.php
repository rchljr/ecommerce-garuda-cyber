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

        // --- Validasi Semua Data dari Form ---
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'short_description' => 'nullable|string|max:500', // Sesuaikan max jika perlu
            'description' => 'nullable|string',
            'modal_price' => 'required|numeric|min:0',
            'profit_percentage' => 'required|numeric|min:0|max:100',
            'sub_category_id' => 'required|exists:sub_categories,id', // Pastikan sub_categories ada
            'main_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // Nullable karena mungkin tidak selalu ada galeri
            'tags' => 'nullable|string', // Tags dikirim sebagai string koma-separated

            // --- Validasi untuk VARIAN PRODUK (dari Alpine.js) ---
            'variants' => 'required|array', // Harus ada setidaknya satu varian
            'variants.*.name' => 'nullable|string|max:255', // Nama varian gabungan (e.g., S / Merah), dikirim dari frontend
            'variants.*.price' => 'required|numeric|min:0', // Harga per varian
            'variants.*.stock' => 'required|integer|min:0', // Stok per varian
            'variants.*.options' => 'required|json', // Data opsi varian dalam format JSON string
            // 'variants.*.image_path' => 'nullable|string', // Jika setiap varian punya path gambar sendiri
        ]);

        DB::beginTransaction(); // Mulai transaksi database

        try {
            // --- 1. Simpan Gambar Utama dan Galeri Produk ---
            $mainImagePath = null;
            if ($request->hasFile('main_image')) {
                $mainImagePath = $request->file('main_image')->store('products/main', 'public');
            }

            $galleryImagePaths = []; // Inisialisasi sebagai array kosong
            if ($request->hasFile('gallery_images')) {
                foreach ($request->file('gallery_images') as $file) {
                    // Pastikan file valid sebelum disimpan
                    if ($file->isValid()) {
                        $galleryImagePaths[] = $file->store('products/gallery', 'public');
                    }
                }
            }

            // --- 2. Buat Produk Utama ---
            $product = Product::create([
                'user_id' => Auth::id(), // Gunakan Auth::id()
                'shop_id' => $shop->id,
                'name' => $validatedData['name'],
                'slug' => Str::slug($validatedData['name']) . '-' . uniqid(), // Pastikan Str diimport
                'short_description' => $validatedData['short_description'],
                'description' => $validatedData['description'],
                'modal_price' => $validatedData['modal_price'],
                'profit_percentage' => $validatedData['profit_percentage'],
                'sku' => $validatedData['sku'] ?? null, // SKu bisa nullable dan tidak wajib
                'sub_category_id' => $validatedData['sub_category_id'],
                'main_image' => $mainImagePath, // Kolom DB 'main_image'
                'gallery_image_paths' => $galleryImagePaths, // Kolom DB 'gallery_image_paths' (tipe JSON)
                'status' => 'active', // Default status produk
                'is_best_seller' => $request->boolean('is_best_seller'), // Gunakan boolean()
                'is_new_arrival' => $request->boolean('is_new_arrival'),
                'is_hot_sale' => $request->boolean('is_hot_sale'),
                // 'tags' tidak disimpan langsung di sini karena ada relasi Many-to-Many
            ]);

            // --- 3. Sinkronkan Tags ---
            if (!empty($validatedData['tags'])) {
                $tagsInput = explode(',', $validatedData['tags']);
                $tagIds = [];
                foreach ($tagsInput as $tagName) {
                    $tag = Tag::firstOrCreate(['name' => trim($tagName)], ['slug' => Str::slug(trim($tagName))]);
                    $tagIds[] = $tag->id;
                }
                $product->tags()->sync($tagIds);
            }

            // --- 4. Simpan Varian Produk ---
            foreach ($validatedData['variants'] as $variantData) {
                $optionsData = json_decode($variantData['options'], true); // Dekode JSON string ke array PHP

                $product->varians()->create([
                    'name' => $variantData['name'] ?? null, // Ambil nama varian dari frontend
                    'price' => $variantData['price'],
                    'stock' => $variantData['stock'],
                    'options_data' => $optionsData, // Simpan array opsi ke kolom JSON
                    // Kolom 'size' dan 'color' di tabel varians sekarang seharusnya null atau dihapus dari DB.
                    'size' => null, // Atur ke null jika kolom masih ada di DB tapi tidak digunakan
                    'color' => null, // Atur ke null jika kolom masih ada di DB tapi tidak digunakan
                    'description' => null, // Atur ke null jika tidak ada deskripsi per varian
                    'status' => 'active', // Atur status default varian
                    'image_path' => null, // Jika ada gambar per varian, perlu logika upload di sini
                ]);
            }

            DB::commit(); // Commit transaksi jika semua berhasil

            return redirect()->route('mitra.products.index')->with('success', 'Produk berhasil ditambahkan!');

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaksi jika terjadi error

            // Hapus gambar yang sudah diupload jika ada error
            if (isset($mainImagePath) && Storage::disk('public')->exists($mainImagePath)) {
                Storage::disk('public')->delete($mainImagePath);
            }
            foreach ($galleryImagePaths as $path) {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }

            // Log error untuk debugging lebih lanjut
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
        $subCategories = collect(); // Mengubah nama variabel dari $categories menjadi $subCategories

        $mainCategorySlug = optional($user->shop)->product_categories;

        if ($mainCategorySlug) {
            $mainCategory = Category::where('slug', $mainCategorySlug)->first();
            if ($mainCategory) {
                $subCategories = $mainCategory->subCategories()->orderBy('name', 'asc')->get();
            }
        }
        return view('dashboard-mitra.products.edit', compact('product', 'subCategories')); // Menggunakan 'subCategories'
    }

    /**
     * Memperbarui produk di database.
     */
    public function update(Request $request, Product $product)
    {
        if ($product->shop_id !== Auth::user()->shop->id) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit produk ini.');
        }

        // --- DEBUGGING: Periksa semua data dari request sebelum validasi ---
        // dd($request->all());

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'short_description' => 'nullable|string',
            'description' => 'nullable|string',
            'modal_price' => 'required|numeric|min:0', // Tambahkan validasi untuk harga modal
            'profit_percentage' => 'required|numeric|min:0|max:100', // Tambahkan validasi untuk persentase keuntungan
            'sub_category_id' => 'required|exists:sub_categories,id',
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

        // --- DEBUGGING: Periksa data yang sudah divalidasi ---
        // dd($validatedData);

        DB::beginTransaction();
        try {
            $updateData = $validatedData;

            unset($updateData['variants'], $updateData['tags']); // Hapus gallery_images karena ditangani terpisah

            // Add checkbox data
            $updateData['is_best_seller'] = $request->has('is_best_seller');
            $updateData['is_new_arrival'] = $request->has('is_new_arrival');
            $updateData['is_hot_sale'] = $request->has('is_hot_sale');

            if ($request->hasFile('main_image')) {
                $this->deleteFile($product->main_image);
                $updateData['main_image'] = $this->uploadFile($request->file('main_image'), 'products/main');
            }

            // Kolom 'price' akan dihitung otomatis oleh mutator di model Product
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

            // Hapus galeri lama jika ada gambar baru yang diunggah
            if ($request->hasFile('gallery_images')) {
                foreach ($product->gallery as $galleryImage) {
                    $this->deleteFile($galleryImage->image_path);
                    $galleryImage->delete();
                }
                foreach ($request->file('gallery_images') as $galleryFile) {
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
        if ($product->shop_id !== Auth::user()->shop->id) { // Tambahkan otorisasi
            abort(403, 'Anda tidak memiliki izin untuk menghapus produk ini.');
        }

        DB::beginTransaction();
        try {
            // Hapus file utama
            $this->deleteFile($product->main_image);

            // Hapus file galeri
            foreach ($product->gallery as $galleryImage) {
                $this->deleteFile($galleryImage->image_path);
            }

            $product->delete(); // Ini akan menghapus relasi seperti variants dan gallery secara otomatis jika onDelete('cascade') di migrasi

            DB::commit();
            return redirect()->route('mitra.products.index')->with('success', 'Produk berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
