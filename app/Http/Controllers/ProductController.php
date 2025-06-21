<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // Tambahkan ini untuk pengelolaan file

class ProductController extends Controller
{
    /**
     * Menampilkan semua produk (halaman penuh).
     * Akan diakses melalui route 'mitra.products.index'.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $products = Product::with('category', 'user')->latest()->get();
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
        $categories = Category::all();
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
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'product_discount' => 'nullable|numeric|min:0|max:100',
            'status' => 'required|in:active,inactive',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $data = $request->all();
        $data['user_id'] = Auth::id(); // Pastikan user_id terisi dari user yang login

        // Upload thumbnail jika ada
        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $filename = time() . '_' . $file->getClientOriginalName();
            // Simpan file ke direktori 'storage/app/public/thumbnails'
            $file->storeAs('public/thumbnails', $filename);
            $data['thumbnail'] = $filename;
        }

        Product::create($data);

        // Redirect ke halaman daftar produk setelah berhasil
        // Gunakan nama rute yang sudah disepakati: 'mitra.products.index'
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
        $categories = Category::all();
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

        // Upload thumbnail baru jika ada
        if ($request->hasFile('thumbnail')) {
            // Hapus thumbnail lama jika ada
            if ($product->thumbnail && Storage::exists('public/thumbnails/' . $product->thumbnail)) {
                Storage::delete('public/thumbnails/' . $product->thumbnail);
            }

            $file = $request->file('thumbnail');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/thumbnails', $filename);
            $data['thumbnail'] = $filename;
        } else {
            // Jika tidak ada thumbnail baru, pastikan thumbnail lama tidak dihapus dari data
            unset($data['thumbnail']); // Hapus dari $data agar tidak menimpa thumbnail lama dengan null
        }

        $product->update($data);

        // Redirect ke halaman daftar produk setelah berhasil
        // Gunakan nama rute yang sudah disepakati: 'mitra.products.index'
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

        // Redirect ke halaman daftar produk setelah berhasil
        // Gunakan nama rute yang sudah disepakati: 'mitra.products.index'
        return redirect()->route('mitra.products.index')->with('success', 'Produk berhasil dihapus.');
    }
}