<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\Product; // Pastikan Product Model sudah terimport
use App\Models\Varian;   // Pastikan Varian Model sudah terimport
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Import Storage facade
use Illuminate\Validation\Rule; // Import Rule for validation
use Illuminate\Support\Facades\DB; // Tambahkan untuk transaksi database

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
        // Catatan: Jika ada gambar per varian, frontend Alpine.js dan validasi di sini perlu disesuaikan.
        $request->validate([
            'variants' => 'required|array', // Pastikan ada array 'variants'
            'variants.*.name' => 'nullable|string|max:255', // Nama varian gabungan (misal: S / Merah)
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.stock' => 'required|integer|min:0',
            'variants.*.options' => 'required|json', // Data opsi varian dalam format JSON string dari Alpine
        ]);

        DB::beginTransaction(); // Mulai transaksi database

        try {
            // Opsional: Hapus semua varian lama yang terkait dengan produk ini
            // Jika Anda ingin sepenuhnya MENGGANTI varian setiap kali form disubmit (mode update/edit product)
            // HATI-HATI: Ini akan menghapus semua varian yang ada untuk produk ini.
            // Biasanya, ini dilakukan di ProductController@update, bukan VarianController@store.
            // Jika ini hanya untuk MENAMBAH varian baru, Anda bisa hapus baris ini.
            // $product->varians()->delete();

            foreach ($request->variants as $variantData) {
                $optionsData = json_decode($variantData['options'], true); // Dekode JSON string ke array PHP

                // Buat 'name' varian dari kombinasi opsi (e.g., "Merah / XL")
                // Menggunakan `implode` yang lebih efisien daripada `join` pada collection
                $variantName = collect($optionsData)->pluck('value')->implode(' / ');

                // Logika penyimpanan gambar per varian jika ada input di frontend:
                // $imagePath = null;
                // if (isset($variantData['image_file']) && $variantData['image_file'] instanceof \Illuminate\Http\UploadedFile) {
                //     $imagePath = $variantData['image_file']->store('variants', 'public');
                // }

                $product->varians()->create([
                    'name' => $variantName, // Nama varian yang dihasilkan (misal: "Merah / XL")
                    'description' => null, // Diasumsikan deskripsi tidak spesifik per varian fleksibel
                    'status' => 'active', // Default status, bisa juga dari frontend jika ada input
                    'price' => $variantData['price'],
                    'stock' => $variantData['stock'],
                    // 'size' dan 'color' akan dihapus atau tidak diisi karena diganti oleh options_data
                    // 'size' => null, // Set null jika kolom masih ada di DB tapi tidak digunakan
                    // 'color' => null, // Set null jika kolom masih ada di DB tapi tidak digunakan
                    'options_data' => $optionsData, // Simpan array opsi ke kolom JSON
                    'image_path' => null, // Atau $imagePath jika ada gambar per varian
                ]);
            }

            DB::commit(); // Commit transaksi jika berhasil

            return redirect()->route('products.show', $product->id) // Sesuaikan rute redirect Anda
                             ->with('success', 'Varian(s) berhasil ditambahkan!');

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaksi jika terjadi error
            // Log error atau tampilkan pesan error yang lebih spesifik
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
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'options' => 'required|json', // Data opsi varian dalam format JSON string
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'clear_image' => 'nullable|boolean',
        ]);

        DB::beginTransaction(); // Mulai transaksi database

        try {
            $imagePath = $varian->image_path; // Ambil path gambar lama

            // Logika upload/hapus gambar
            if ($request->hasFile('image')) {
                if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath); // Hapus gambar lama
                }
                $imagePath = $request->file('image')->store('variants', 'public'); // Simpan gambar baru
            } elseif ($request->boolean('clear_image')) { // Gunakan boolean() untuk input checkbox
                if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath); // Hapus gambar lama jika dicentang
                }
                $imagePath = null;
            }

            $optionsData = json_decode($request->options, true); // Dekode JSON string

            // Siapkan data untuk update
            $updateData = [
                'name' => $request->name,
                'price' => $request->price,
                'stock' => $request->stock,
                'options_data' => $optionsData, // Update kolom JSON
                'image_path' => $imagePath,
                // Kolom lain yang mungkin di-update atau tetap
                'description' => $request->description ?? $varian->description,
                'status' => $request->status ?? $varian->status,
                'size' => null, // Set null jika kolom masih ada di DB tapi tidak digunakan
                'color' => null, // Set null jika kolom masih ada di DB tapi tidak digunakan
            ];

            $varian->update($updateData);

            DB::commit(); // Commit transaksi jika berhasil

            return redirect()->route('products.show', $varian->product_id) // Sesuaikan rute redirect Anda
                             ->with('success', 'Varian berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaksi jika terjadi error
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
        DB::beginTransaction(); // Mulai transaksi database

        try {
            // Hapus gambar terkait jika ada
            if ($varian->image_path && Storage::disk('public')->exists($varian->image_path)) {
                Storage::disk('public')->delete($varian->image_path);
            }

            $varian->delete();

            DB::commit(); // Commit transaksi jika berhasil

            return redirect()->route('products.show', $varian->product_id) // Sesuaikan rute redirect Anda
                             ->with('success', 'Varian berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaksi jika terjadi error
            return redirect()->back()->withErrors(['error' => 'Gagal menghapus varian: ' . $e->getMessage()]);
        }
    }
}