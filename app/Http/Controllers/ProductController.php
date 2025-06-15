<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    // Menampilkan semua produk
    public function index()
    {
        $products = Product::with('category', 'user')->latest()->get();
        return view('dashboard-mitra.products.index', compact('products'));
    }

    // Menampilkan form tambah produk
    public function create()
    {
        $categories = Category::all();
        return view('dashboard-mitra.products.create', compact('categories'));
    }

    // Simpan produk baru
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
        $data['user_id'] = Auth::id();

        // Upload thumbnail
        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/thumbnails', $filename);
            $data['thumbnail'] = $filename;
        }

        Product::create($data);

        return redirect()->route('dashboard-mitra.products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    // Menampilkan detail produk
    public function show($id)
    {
        $product = Product::with('category', 'user')->findOrFail($id);
        return view('dashboard-mitra.products.show', compact('product'));
    }

    // Menampilkan form edit
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::all();
        return view('dashboard-mitra.products.edit', compact('product', 'categories'));
    }

    // Simpan perubahan produk
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

        // Upload thumbnail jika ada
        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/thumbnails', $filename);
            $data['thumbnail'] = $filename;
        }

        $product->update($data);

        return redirect()->route('dashboard-mitra.products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    // Hapus produk
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->route('dashboard-mitra.products.index')->with('success', 'Produk berhasil dihapus.');
    }
}
