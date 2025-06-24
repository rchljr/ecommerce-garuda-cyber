<?php

namespace App\Http\Controllers; // Ini seharusnya 'namespace App\Http\Controllers\Admin;' jika Anda ingin ini di folder Admin

use App\Models\Product;
use App\Models\Category; // Tambahkan ini jika belum
use App\Models\SubCategory; // Tambahkan ini jika belum
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Penting: Untuk Auth::id()
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str; // Tambahkan ini untuk Str::slug()

class ProductController extends Controller // Jika ini controller admin, namespace harus App\Http\Controllers\Admin
{
    /**
     * Menampilkan semua produk (halaman penuh).
     * Akan diakses melalui route 'mitra.products.index'.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $products = Product::with('category', 'user')->latest()->get(); // Pastikan relasi 'category' dan 'user' ada di Model Product
        return view('dashboard-mitra.products.index', compact('products'));
    }

    /**
     * Menampilkan form untuk membuat produk baru.
     * Akan diakses melalui route 'mitra.products.create'.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $categories = Category::all(); // Pastikan Model Category ada
        return view('dashboard-mitra.products.create', compact('categories'));
    }

    /**
     * Menyimpan produk baru ke database.
     * Akan diakses melalui route 'mitra.products.store'.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id', // Pastikan tabel 'categories' ada
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'product_discount' => 'nullable|numeric|min:0|max:100',
            'status' => 'required|in:active,inactive',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name); // Tambahkan slug secara otomatis
        $data['is_active'] = $request->boolean('is_active') ?? true; // Pastikan is_active diset, default true
        
        // --- PERBAIKAN DI SINI ---
        // AKTIFKAN BARIS INI
        $data['user_id'] = Auth::id(); // Mengambil ID pengguna yang sedang login
        // --- AKHIR PERBAIKAN ---

        // Upload thumbnail jika ada
        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            // Gunakan Str::random() atau UUID untuk nama file yang unik untuk menghindari konflik
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            // Simpan file ke direktori 'storage/app/public/thumbnails'
            $file->storeAs('public/thumbnails', $filename);
            $data['thumbnail'] = $filename;
        } else {
            $data['thumbnail'] = null; // Pastikan null jika tidak ada thumbnail
        }

        Product::create($data);

        return redirect()->route('mitra.products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail produk tertentu.
     * Akan diakses melalui route 'mitra.products.show'.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        // Temukan produk berdasarkan ID, beserta kategori dan user terkait
        $product = Product::with('category', 'user')->findOrFail($id);
        return view('dashboard-mitra.products.show', compact('product'));
    }

    /**
     * Menampilkan form untuk mengedit produk tertentu.
     * Akan diakses melalui route 'mitra.products.edit'.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        // Temukan produk yang akan diedit
        $product = Product::findOrFail($id);
        // Ambil semua kategori untuk dropdown
        $categories = Category::all(); // Harusnya Category::all() jika kategori utama, bukan SubCategory
        return view('dashboard-mitra.products.edit', compact('product', 'categories'));
    }

    /**
     * Memperbarui produk tertentu di database.
     * Akan diakses melalui route 'mitra.products.update'.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'product_discount' => 'nullable|numeric|min:0|max:100',
            'status' => 'required|in:active,inactive',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name); // Tambahkan slug secara otomatis
        $data['is_active'] = $request->boolean('is_active') ?? $product->is_active; // Pertahankan nilai lama jika checkbox tidak ada

        // Upload thumbnail baru jika ada
        if ($request->hasFile('thumbnail')) {
            // Hapus thumbnail lama jika ada
            if ($product->thumbnail && Storage::exists('public/thumbnails/' . $product->thumbnail)) {
                Storage::delete('public/thumbnails/' . $product->thumbnail);
            }

            $file = $request->file('thumbnail');
            // Gunakan Str::random() atau UUID untuk nama file yang unik untuk menghindari konflik
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/thumbnails', $filename);
            $data['thumbnail'] = $filename;
        } else {
            // Jika tidak ada thumbnail baru, pastikan thumbnail lama tidak dihapus dari data
            // Cek jika ada input yang secara eksplisit ingin menghapus thumbnail
            if ($request->has('remove_thumbnail')) { // Asumsi ada checkbox di form untuk remove thumbnail
                if ($product->thumbnail && Storage::exists('public/thumbnails/' . $product->thumbnail)) {
                    Storage::delete('public/thumbnails/' . $product->thumbnail);
                }
                $data['thumbnail'] = null;
            } else {
                $data['thumbnail'] = $product->thumbnail; // Pertahankan thumbnail lama
            }
        }
        
        $product->update($data);

        return redirect()->route('mitra.products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    /**
     * Menghapus produk tertentu dari database.
     * Akan diakses melalui route 'mitra.products.destroy'.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        // Hapus thumbnail dari storage jika ada
        if ($product->thumbnail && Storage::exists('public/thumbnails/' . $product->thumbnail)) {
            Storage::delete('public/thumbnails/' . $product->thumbnail);
        }

        $product->delete();

        return redirect()->route('mitra.products.index')->with('success', 'Produk berhasil dihapus.');
    }
}
