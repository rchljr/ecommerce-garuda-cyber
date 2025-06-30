<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BannerController extends Controller
{
    /**
     * Menampilkan daftar semua item banner.
     */
    public function index()
    {
        $banners = Banner::orderBy('order')->latest()->paginate(10);
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
        $validatedData = $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'image' => 'required|image|mimes:jpg,jpeg,png,gif|max:5048', // Gambar wajib
            'link_url' => 'nullable|url|max:255',
            'button_text' => 'nullable|string|max:100',
            'is_active' => 'boolean', // Akan ditangani oleh hidden input + cast
            'order' => 'required|integer|min:0',
        ]);

        if ($request->hasFile('image')) {
            $imageName = time() . '_' . Str::random(10) . '.' . $request->file('image')->getClientOriginalExtension();
            $validatedData['image'] = $request->file('image')->storeAs('banner_images', $imageName, 'public');
        }

        Banner::create($validatedData);

        return redirect()->route('mitra.banners.index')->with('success', 'Banner item berhasil ditambahkan!');
    }

    /**
     * Menampilkan detail item banner (opsional, bisa diabaikan).
     */
    public function show(Banner $banner)
    {
        return view('dashboard-mitra.banners.show', compact('banner'));
    }

    /**
     * Menampilkan form untuk mengedit item banner.
     */
    public function edit(Banner $banner)
    {
        return view('dashboard-mitra.banners.edit', compact('banner'));
    }

    /**
     * Memperbarui item banner di database.
     */
    public function update(Request $request, Banner $banner)
    {
        $validatedData = $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:5048', // Bisa nullable saat update (jika tidak diganti)
            'link_url' => 'nullable|url|max:255',
            'button_text' => 'nullable|string|max:100',
            'is_active' => 'boolean',
            'order' => 'required|integer|min:0',
        ]);

        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($banner->image && Storage::disk('public')->exists($banner->image)) {
                Storage::disk('public')->delete($banner->image);
            }
            $imageName = time() . '_' . Str::random(10) . '.' . $request->file('image')->getClientOriginalExtension();
            $validatedData['image'] = $request->file('image')->storeAs('banner_images', $imageName, 'public');
        } elseif ($request->input('remove_image')) { // Jika ada input hidden untuk menghapus gambar
            if ($banner->image && Storage::disk('public')->exists($banner->image)) {
                Storage::disk('public')->delete($banner->image);
            }
            $validatedData['image'] = null;
        } else {
            // Jika tidak ada gambar baru diupload dan tidak diminta untuk dihapus, pertahankan yang lama
            $validatedData['image'] = $banner->image;
        }

        $banner->update($validatedData);

        return redirect()->route('mitra.banners.index')->with('success', 'Banner item berhasil diperbarui!');
    }

    /**
     * Menghapus item banner dari database.
     */
    public function destroy(Banner $banner)
    {
        // Hapus gambar dari storage jika ada
        if ($banner->image && Storage::disk('public')->exists($banner->image)) {
            Storage::disk('public')->delete($banner->image);
        }

        $banner->delete();

        return redirect()->route('mitra.banners.index')->with('success', 'Banner item berhasil dihapus!');
    }
}
