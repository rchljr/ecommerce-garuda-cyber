<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\Hero; // Pastikan mengarah ke model tenant
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HeroController extends Controller
{
    /**
     * Menampilkan daftar semua item hero milik tenant.
     */
    public function index()
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        if (!$tenant) {
            abort(403, 'Tenant tidak ditemukan.');
        }

        // Jalankan query untuk mengambil hero di dalam database tenant
        $heroes = $tenant->execute(function () {
            return Hero::orderBy('order')->latest()->paginate(10);
        });

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
     * Menyimpan item hero baru ke database tenant.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'image' => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:5048',
            'button_text' => 'nullable|string|max:100',
            'button_url' => 'nullable|url|max:255',
            'is_active' => 'nullable', // Diubah agar lebih fleksibel
            'order' => 'required|integer|min:0',
        ]);

        $user = Auth::user();
        $tenant = $user->tenant;

        if (!$tenant) {
            return back()->with('error', 'Tenant tidak ditemukan.');
        }

        // Jalankan operasi pembuatan hero di dalam database tenant
        $tenant->execute(function () use ($validatedData, $request, $user) {
            
            $heroData = $validatedData;

            if ($request->hasFile('image')) {
                $imageName = time() . '_' . Str::random(10) . '.' . $request->file('image')->getClientOriginalExtension();
                $heroData['image'] = $request->file('image')->storeAs('hero_images', $imageName, 'public');
            }
            
            // PERBAIKAN FINAL: Buat instance baru secara manual untuk menghindari masalah mass assignment
            $hero = new Hero($heroData);
            $hero->user_id = $user->id; // Set user_id secara eksplisit
            $hero->is_active = $request->has('is_active'); // Set status aktif dari checkbox
            $hero->save(); // Simpan model
        });

        return redirect()->route('mitra.heroes.index')->with('success', 'Hero berhasil ditambahkan!');
    }

    /**
     * Menampilkan form untuk mengedit item hero.
     */
    public function edit($heroId) // Terima ID sebagai string
    {
        $tenant = Auth::user()->tenant;
        if (!$tenant) {
            abort(404);
        }

        // Cari hero di dalam database tenant
        $hero = $tenant->execute(function () use ($heroId) {
            return Hero::findOrFail($heroId);
        });

        return view('dashboard-mitra.heroes.edit', compact('hero'));
    }

    /**
     * Memperbarui item hero di database tenant.
     */
    public function update(Request $request, $heroId) // Terima ID sebagai string
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:5048',
            'button_text' => 'nullable|string|max:100',
            'button_url' => 'nullable|url|max:255',
            'is_active' => 'nullable', // Diubah agar lebih fleksibel
            'order' => 'required|integer|min:0',
        ]);
        
        $tenant = Auth::user()->tenant;
        if (!$tenant) {
            return back()->with('error', 'Tenant tidak ditemukan.');
        }

        // Jalankan operasi update di dalam database tenant
        $tenant->execute(function () use ($validatedData, $request, $heroId) {
            $hero = Hero::findOrFail($heroId);

            $dataToUpdate = $validatedData;
            $dataToUpdate['is_active'] = $request->has('is_active'); // Set status aktif dari checkbox

            if ($request->hasFile('image')) {
                if ($hero->image && Storage::disk('public')->exists($hero->image)) {
                    Storage::disk('public')->delete($hero->image);
                }
                $imageName = time() . '_' . Str::random(10) . '.' . $request->file('image')->getClientOriginalExtension();
                $dataToUpdate['image'] = $request->file('image')->storeAs('hero_images', $imageName, 'public');
            }

            $hero->update($dataToUpdate);
        });

        return redirect()->route('mitra.heroes.index')->with('success', 'Hero berhasil diperbarui!');
    }

    /**
     * Menghapus item hero dari database tenant.
     */
    public function destroy($heroId) // Terima ID sebagai string
    {
        $tenant = Auth::user()->tenant;
        if (!$tenant) {
            return back()->with('error', 'Tenant tidak ditemukan.');
        }

        // Jalankan operasi penghapusan di dalam database tenant
        $tenant->execute(function () use ($heroId) {
            $hero = Hero::findOrFail($heroId);

            if ($hero->image && Storage::disk('public')->exists($hero->image)) {
                Storage::disk('public')->delete($hero->image);
            }
            $hero->delete();
        });

        return redirect()->route('mitra.heroes.index')->with('success', 'Hero berhasil dihapus!');
    }
}
