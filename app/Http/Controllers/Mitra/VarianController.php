<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Varian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str; // Untuk Slug atau UUID jika diperlukan
use Illuminate\Support\Facades\Log; // Untuk logging

class VarianController extends Controller
{
    /**
     * Menyimpan varian baru (fleksibel) untuk produk tertentu.
     * Metode ini akan menerima array varian dari frontend Alpine.js.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Product $product)
    {
        // Validasi untuk seluruh array varian yang dikirim dari frontend
        $request->validate([
            'variants' => 'required|array',
            'variants.*.name' => 'nullable|string|max:255',
            'variants.*.modal_price' => 'required|numeric|min:0', // BARU: Validasi modal_price per varian
            'variants.*.profit_percentage' => 'required|numeric|min:0|max:100', // BARU: Validasi profit_percentage per varian
            'variants.*.stock' => 'required|integer|min:0',
            'variants.*.options' => 'required|json', // Data opsi varian dalam format JSON string
        ]);

        DB::beginTransaction();

        try {
            foreach ($request->variants as $variantData) {
                $optionsData = json_decode($variantData['options'], true);

                // Buat 'name' varian dari kombinasi opsi
                $variantName = collect($optionsData)->pluck('value')->implode(' / ');

                // Hitung harga jual varian
                $sellingPrice = (float) $variantData['modal_price'] * (1 + ((float) $variantData['profit_percentage'] / 100));

                $product->varians()->create([
                    'name' => $variantName, // Nama varian yang dihasilkan (misal: "Merah / XL")
                    'modal_price' => $variantData['modal_price'], // Simpan harga modal varian
                    'profit_percentage' => $variantData['profit_percentage'], // Simpan persentase profit varian
                    'price' => $sellingPrice, // Simpan harga jual di kolom 'price'
                    'stock' => $variantData['stock'],
                    'options_data' => $optionsData, // Simpan array opsi ke kolom JSON
                    // Hapus 'size' dan 'color' jika sudah tidak ada di DB
                    // 'size' => null,
                    // 'color' => null,
                    'description' => null, // Default
                    'status' => 'active', // Default
                    'image_path' => null, // Default atau jika ada logika upload gambar varian
                ]);
            }

            DB::commit();

            return redirect()->route('mitra.products.show', $product->id) // Sesuaikan rute redirect Anda
                             ->with('success', 'Varian(s) berhasil ditambahkan!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error saving varian(s): " . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->withInput()->withErrors(['error' => 'Gagal menyimpan varian: ' . $e->getMessage()]);
        }
    }

    /**
     * Menampilkan formulir untuk mengedit varian tunggal.
     * Data `options_data` akan diteruskan ke view untuk dirender oleh Alpine.js.
     *
     * @param  \App\Models\Varian  $varian
     * @return \Illuminate\View\View
     */
    public function edit(Varian $varian)
    {
        // Pastikan varian terkait dengan produk yang benar jika perlu validasi lebih lanjut
        // Kita akan meneruskan data varian, termasuk options_data
        return view('varians.edit', compact('varian')); // Sesuaikan view path Anda
    }

    /**
     * Memperbarui varian tunggal di database.
     * Mengakomodasi perubahan pada data opsi dinamis (`options_data`).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Varian  $varian
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Varian $varian)
    {
        $request->validate([
            'name' => 'nullable|string|max:255', // Nama varian gabungan (opsional)
            'modal_price' => 'required|numeric|min:0', // BARU: Validasi modal_price
            'profit_percentage' => 'required|numeric|min:0|max:100', // BARU: Validasi profit_percentage
            'stock' => 'required|integer|min:0',
            'options' => 'required|json', // Data opsi varian dalam format JSON string
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'clear_image' => 'nullable|boolean',
        ]);

        DB::beginTransaction();

        try {
            $imagePath = $varian->image_path;

            if ($request->hasFile('image')) {
                if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }
                $imagePath = $request->file('image')->store('variants', 'public');
            } elseif ($request->boolean('clear_image') && $varian->image_path) {
                if (Storage::disk('public')->exists($varian->image_path)) {
                    Storage::disk('public')->delete($varian->image_path);
                }
                $imagePath = null;
            }

            $optionsData = json_decode($request->options, true);

            // Hitung harga jual varian yang baru
            $sellingPrice = (float) $request->modal_price * (1 + ((float) $request->profit_percentage / 100));

            $updateData = [
                'name' => $request->name,
                'modal_price' => $request->modal_price, // Update modal_price
                'profit_percentage' => $request->profit_percentage, // Update profit_percentage
                'price' => $sellingPrice, // Update harga jual
                'stock' => $request->stock,
                'options_data' => $optionsData,
                'image_path' => $imagePath,
                // Kolom lain yang mungkin di-update atau tetap
                'description' => $request->description ?? $varian->description,
                'status' => $request->status ?? $varian->status,
                // Hapus 'size' dan 'color' jika sudah tidak ada di DB
                // 'size' => null,
                // 'color' => null,
            ];

            $varian->update($updateData);

            DB::commit();

            return redirect()->route('mitra.products.show', $varian->product_id)
                             ->with('success', 'Varian berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error updating varian: " . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->withInput()->withErrors(['error' => 'Gagal memperbarui varian: ' . $e->getMessage()]);
        }
    }

    /**
     * Menghapus varian dari database.
     *
     * @param  \App\Models\Varian  $varian
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Varian $varian)
    {
        DB::beginTransaction();

        try {
            // Hapus gambar terkait jika ada
            if ($varian->image_path && Storage::disk('public')->exists($varian->image_path)) {
                Storage::disk('public')->delete($varian->image_path);
            }

            $varian->delete();

            DB::commit();

            return redirect()->route('mitra.products.show', $varian->product_id)
                             ->with('success', 'Varian berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error deleting varian: " . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->withErrors(['error' => 'Gagal menghapus varian: ' . $e->getMessage()]);
        }
    }
}