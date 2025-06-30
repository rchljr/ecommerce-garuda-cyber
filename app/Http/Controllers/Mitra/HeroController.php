<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\Hero;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HeroController extends Controller
{
    /**
     * Menampilkan daftar semua item hero.
     */
    public function index()
    {
        $heroes = Hero::orderBy('order')->latest()->paginate(10);
        // PERBAIKAN: Mengubah jalur tampilan ke 'mitra.heroes.index'
        return view('dashboard-mitra.heroes.index', compact('heroes'));
    }

    /**
     * Menampilkan form untuk membuat item hero baru.
     */
    public function create()
    {
        // PERBAIKAN: Mengubah jalur tampilan ke 'mitra.heroes.create'
        return view('dashboard-mitra.heroes.create');
    }

    /**
     * Menyimpan item hero baru ke database.
     */
    public function store(Request $request)
    {
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
            $imageName = time() . '_' . Str::random(10) . '.' . $request->file('image')->getClientOriginalExtension();
            $validatedData['image'] = $request->file('image')->storeAs('hero_images', $imageName, 'public');
        } else {
            $validatedData['image'] = null;
        }

        Hero::create($validatedData);

        return redirect()->route('mitra.heroes.index')->with('success', 'Hero item berhasil ditambahkan!');
    }

    /**
     * Menampilkan detail item hero.
     */
    public function show(Hero $hero)
    {
        // PERBAIKAN: Mengubah jalur tampilan ke 'mitra.heroes.show'
        return view('dashboard-mitra.heroes.show', compact('hero'));
    }

    /**
     * Menampilkan form untuk mengedit item hero.
     */
    public function edit(Hero $hero)
    {
        // PERBAIKAN: Mengubah jalur tampilan ke 'mitra.heroes.edit'
        return view('dashboard-mitra.heroes.edit', compact('hero'));
    }

    /**
     * Memperbarui item hero di database.
     */
    public function update(Request $request, Hero $hero)
    {
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

    /**
     * Menghapus item hero dari database.
     */
    public function destroy(Hero $hero)
    {
        if ($hero->image && Storage::disk('public')->exists($hero->image)) {
            Storage::disk('public')->delete($hero->image);
        }

        $hero->delete();

        return redirect()->route('mitra.heroes.index')->with('success', 'Hero item berhasil dihapus!');
    }
}
