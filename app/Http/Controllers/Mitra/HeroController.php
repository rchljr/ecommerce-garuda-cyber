<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\Hero;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <-- Import Auth
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HeroController extends Controller
{
    /**
     * Menampilkan daftar hero milik mitra yang sedang login.
     */
    public function index()
    {
        // PERBAIKAN: Ambil hero milik shop dari user yang login
        $heroes = Auth::user()->shop->heroes()->orderBy('order')->latest()->paginate(10);
        return view('dashboard-mitra.heroes.index', compact('heroes'));
    }

    /**
     * Menampilkan form untuk membuat item hero baru.
     */
    public function create()
    {
        return view('dashboard-mitra.heroes.create');
    }

    /**
     * Menyimpan item hero baru ke database dan menghubungkannya ke shop.
     */
    public function store(Request $request)
    {
        // Ganti komentar validasi Anda dengan aturan yang lengkap
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:5048',
            'button_text' => 'nullable|string|max:100',
            'button_url' => 'nullable|url|max:255',
            'is_active' => 'nullable|boolean',
            'order' => 'required|integer|min:0',
        ]);

        // TAMBAHKAN BARIS INI UNTUK DEBUG
        // dd($validatedData); 

        $validatedData['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            $imageName = time() . '_' . Str::random(10) . '.' . $request->file('image')->getClientOriginalExtension();
            $validatedData['image'] = $request->file('image')->storeAs('hero_images', $imageName, 'public');
        }

        Auth::user()->shop->heroes()->create($validatedData);

        return redirect()->route('mitra.heroes.index')->with('success', 'Hero item berhasil ditambahkan!');
    }

    /**
     * Menampilkan form untuk mengedit item hero.
     */
    public function edit(Hero $hero)
    {
        // OTORISASI: Pastikan hero ini milik toko yang benar
        if ($hero->shop_id !== Auth::user()->shop->id) {
            abort(403, 'Akses Ditolak');
        }
        return view('dashboard-mitra.heroes.edit', compact('hero'));
    }

    /**
     * Memperbarui item hero di database.
     */
    public function update(Request $request, Hero $hero)
    {
        // OTORISASI
        if ($hero->shop_id !== Auth::user()->shop->id) {
            abort(403, 'Akses Ditolak');
        } {
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'subtitle' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:5048',
                'button_text' => 'nullable|string|max:100',
                'button_url' => 'nullable|url|max:255',
                'is_active' => 'nullable|boolean',
                'order' => 'required|integer|min:0',
            ]);

            // Mengonversi 'is_active' dari 'on' atau null ke true/false
            $validatedData['is_active'] = $request->has('is_active');

            if ($request->hasFile('image')) {
                if ($hero->image && Storage::disk('public')->exists($hero->image)) {
                    Storage::disk('public')->delete($hero->image);
                }
                $imageName = time() . '_' . Str::random(10) . '.' . $request->file('image')->getClientOriginalExtension();
                $validatedData['image'] = $request->file('image')->storeAs('hero_images', $imageName, 'public');
            } elseif ($request->input('remove_image')) {
                if ($hero->image && Storage::disk('public')->exists($hero->image)) {
                    Storage::disk('public')->delete($hero->image);
                }
                $validatedData['image'] = null;
            } else {
                // Jika tidak ada gambar baru diupload dan tidak diminta untuk dihapus, pertahankan yang lama
                $validatedData['image'] = $hero->image;
            }

            $hero->update($validatedData);

            return redirect()->route('mitra.heroes.index')->with('success', 'Hero item berhasil diperbarui!');
        }
    }

    /**
     * Menghapus item hero dari database.
     */
    public function destroy(Hero $hero)
    {
        // OTORISASI
        if ($hero->shop_id !== Auth::user()->shop->id) {
            abort(403, 'Akses Ditolak');
        }

        // ... Logika hapus Anda sudah benar ...
        $hero->delete();

        return redirect()->route('mitra.heroes.index')->with('success', 'Hero item berhasil dihapus!');
    }
}
