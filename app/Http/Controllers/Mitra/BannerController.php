<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Traits\UploadFile; // Import Trait untuk upload file
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BannerController extends Controller
{
    use UploadFile; // Gunakan Trait di dalam class

    /**
     * Menampilkan daftar semua item banner milik mitra.
     */
    public function index()
    {
        $user = Auth::user();
        $shop = $user->shop;

        // Pastikan mitra memiliki toko
        if (!$shop) {
            abort(403, 'Profil toko Anda belum lengkap.');
        }

        // Ambil banner yang HANYA memiliki shop_id yang sama dengan toko mitra.
        $banners = Banner::where('shop_id', $shop->id)
            ->orderBy('order')
            ->latest()
            ->paginate(10);

        return view('dashboard-mitra.banners.index', compact('banners'));
    }

    /**
     * Menampilkan form untuk membuat item banner baru.
     */
    public function create()
    {
        return view('dashboard-mitra.banners.create');
    }

    /**
     * Menyimpan item banner baru ke database.
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
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'image' => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:5048',
            'link_url' => 'nullable|url|max:255',
            'button_text' => 'nullable|string|max:100',
            'is_active' => 'boolean',
            'order' => 'required|integer|min:0',
        ]);

        // Tambahkan shop_id dan user_id secara otomatis
        $validatedData['shop_id'] = $shop->id;
        $validatedData['user_id'] = $user->id;
        $validatedData['is_active'] = $request->has('is_active');


        if ($request->hasFile('image')) {
            // Gunakan Trait untuk upload file
            $validatedData['image'] = $this->uploadFile($request->file('image'), 'banner_images');
        }

        Banner::create($validatedData);

        return redirect()->route('mitra.banners.index')->with('success', 'Banner item berhasil ditambahkan!');
    }

    /**
     * Menampilkan form untuk mengedit item banner.
     */
    public function edit(Banner $banner)
    {
        // Pengecekan Otorisasi: Pastikan banner ini milik toko mitra yang sedang login
        if ($banner->shop_id !== Auth::user()->shop->id) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit banner ini.');
        }
        return view('dashboard-mitra.banners.edit', compact('banner'));
    }

    /**
     * Memperbarui item banner di database.
     */
    public function update(Request $request, Banner $banner)
    {
        // Pengecekan Otorisasi
        if ($banner->shop_id !== Auth::user()->shop->id) {
            abort(403, 'Anda tidak memiliki izin untuk memperbarui banner ini.');
        }

        $validatedData = $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:5048',
            'link_url' => 'nullable|url|max:255',
            'button_text' => 'nullable|string|max:100',
            'is_active' => 'boolean',
            'order' => 'required|integer|min:0',
        ]);

        $validatedData['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            // Hapus gambar lama menggunakan Trait
            $this->deleteFile($banner->image);
            // Upload gambar baru menggunakan Trait
            $validatedData['image'] = $this->uploadFile($request->file('image'), 'banner_images');
        }

        $banner->update($validatedData);

        return redirect()->route('mitra.banners.index')->with('success', 'Banner item berhasil diperbarui!');
    }

    /**
     * Menghapus item banner dari database.
     */
    public function destroy(Banner $banner)
    {
        // Pengecekan Otorisasi
        if ($banner->shop_id !== Auth::user()->shop->id) {
            abort(403, 'Anda tidak memiliki izin untuk menghapus banner ini.');
        }

        // Hapus gambar dari storage menggunakan Trait
        $this->deleteFile($banner->image);

        $banner->delete();

        return redirect()->route('mitra.banners.index')->with('success', 'Banner item berhasil dihapus!');
    }
}
