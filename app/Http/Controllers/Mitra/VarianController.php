<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Varian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Import Storage facade
use Illuminate\Validation\Rule; // Import Rule for validation

class VarianController extends Controller
{
    /**
     * Menyimpan varian baru untuk produk tertentu.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'size' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validasi gambar
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('variants', 'public');
        }

        $product->varians()->create([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status,
            'price' => $request->price,
            'stock' => $request->stock,
            'size' => $request->size,
            'color' => $request->color,
            'image_path' => $imagePath,
        ]);

        return redirect()->route('products.show', $product->id)
                         ->with('success', 'Varian berhasil ditambahkan!');
    }

    /**
     * Menampilkan formulir untuk mengedit varian.
     *
     * @param  \App\Models\Varian  $varian
     * @return \Illuminate\View\View
     */
    public function edit(Varian $varian)
    {
        // Pastikan varian terkait dengan produk yang benar jika perlu validasi lebih lanjut
        return view('varians.edit', compact('varian'));
    }

    /**
     * Memperbarui varian di database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Varian  $varian
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Varian $varian)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'size' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $imagePath = $varian->image_path; // Ambil path gambar lama

        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            // Simpan gambar baru
            $imagePath = $request->file('image')->store('variants', 'public');
        } elseif ($request->input('clear_image')) {
            // Jika checkbox "Hapus Gambar" dicentang
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = null;
        }

        $varian->update(array_merge(
            $request->except(['image', 'clear_image']), // Jangan update 'image' dan 'clear_image' langsung
            ['image_path' => $imagePath] // Update 'image_path' secara manual
        ));

        return redirect()->route('products.show', $varian->product_id)
                         ->with('success', 'Varian berhasil diperbarui.');
    }

    /**
     * Menghapus varian dari database.
     *
     * @param  \App\Models\Varian  $varian
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Varian $varian)
    {
        // Hapus gambar terkait jika ada
        if ($varian->image_path && Storage::disk('public')->exists($varian->image_path)) {
            Storage::disk('public')->delete($varian->image_path);
        }

        $varian->delete();

        return redirect()->route('products.show', $varian->product_id)
                         ->with('success', 'Varian berhasil dihapus.');
    }
}
