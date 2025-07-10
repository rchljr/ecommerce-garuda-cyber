<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\Banner; // Pastikan mengarah ke model tenant
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BannerController extends Controller
{
    /**
     * Menampilkan daftar semua item banner milik tenant.
     */
    public function index()
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        if (!$tenant) {
            abort(403, 'Tenant tidak ditemukan.');
        }

        // Jalankan query untuk mengambil banner di dalam database tenant
        $banners = $tenant->execute(function () {
            return Banner::orderBy('order')->latest()->paginate(10);
        });

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
     * Menyimpan item banner baru ke database tenant.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'image' => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:5048',
            'link_url' => 'nullable|url|max:255',
            'button_text' => 'nullable|string|max:100',
            'is_active' => 'nullable',
            'order' => 'required|integer|min:0',
        ]);

        $user = Auth::user();
        $tenant = $user->tenant;

        if (!$tenant) {
            return back()->with('error', 'Tenant tidak ditemukan.');
        }

        // Jalankan operasi pembuatan banner di dalam database tenant
        $tenant->execute(function () use ($validatedData, $request, $user) {
            
            $bannerData = $validatedData;

            if ($request->hasFile('image')) {
                $imageName = time() . '_' . Str::random(10) . '.' . $request->file('image')->getClientOriginalExtension();
                $bannerData['image'] = $request->file('image')->storeAs('banner_images', $imageName, 'public');
            }
            
            $banner = new Banner($bannerData);
            $banner->user_id = $user->id;
            $banner->is_active = $request->has('is_active');
            $banner->save();
        });

        return redirect()->route('mitra.banners.index')->with('success', 'Banner berhasil ditambahkan!');
    }

    /**
     * Menampilkan form untuk mengedit item banner.
     */
    public function edit($bannerId) // Terima ID sebagai string
    {
        $tenant = Auth::user()->tenant;
        if (!$tenant) {
            abort(404);
        }

        // Cari banner di dalam database tenant
        $banner = $tenant->execute(function () use ($bannerId) {
            return Banner::findOrFail($bannerId);
        });

        return view('dashboard-mitra.banners.edit', compact('banner'));
    }

    /**
     * Memperbarui item banner di database tenant.
     */
    public function update(Request $request, $bannerId) // Terima ID sebagai string
    {
        $validatedData = $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:5048',
            'link_url' => 'nullable|url|max:255',
            'button_text' => 'nullable|string|max:100',
            'is_active' => 'nullable',
            'order' => 'required|integer|min:0',
        ]);
        
        $tenant = Auth::user()->tenant;
        if (!$tenant) {
            return back()->with('error', 'Tenant tidak ditemukan.');
        }

        // Jalankan operasi update di dalam database tenant
        $tenant->execute(function () use ($validatedData, $request, $bannerId) {
            $banner = Banner::findOrFail($bannerId);

            $dataToUpdate = $validatedData;
            $dataToUpdate['is_active'] = $request->has('is_active');

            if ($request->hasFile('image')) {
                if ($banner->image && Storage::disk('public')->exists($banner->image)) {
                    Storage::disk('public')->delete($banner->image);
                }
                $imageName = time() . '_' . Str::random(10) . '.' . $request->file('image')->getClientOriginalExtension();
                $dataToUpdate['image'] = $request->file('image')->storeAs('banner_images', $imageName, 'public');
            }

            $banner->update($dataToUpdate);
        });

        return redirect()->route('mitra.banners.index')->with('success', 'Banner berhasil diperbarui!');
    }

    /**
     * Menghapus item banner dari database tenant.
     */
    public function destroy($bannerId) // Terima ID sebagai string
    {
        $tenant = Auth::user()->tenant;
        if (!$tenant) {
            return back()->with('error', 'Tenant tidak ditemukan.');
        }

        // Jalankan operasi penghapusan di dalam database tenant
        $tenant->execute(function () use ($bannerId) {
            $banner = Banner::findOrFail($bannerId);

            if ($banner->image && Storage::disk('public')->exists($banner->image)) {
                Storage::disk('public')->delete($banner->image);
            }
            $banner->delete();
        });

        return redirect()->route('mitra.banners.index')->with('success', 'Banner berhasil dihapus!');
    }
}
