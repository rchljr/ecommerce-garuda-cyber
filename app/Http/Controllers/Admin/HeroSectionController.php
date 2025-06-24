<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HeroSectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $heroSections = HeroSection::all();
        return view('dashboard-mitra.hero_sections.index', compact('heroSections'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard-mitra.hero_sections.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'button_text' => 'nullable|string|max:255',
            'button_url' => 'nullable|string|max:255',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $data = $request->except('background_image');

        if ($request->hasFile('background_image')) {
            $imagePath = $request->file('background_image')->store('hero_banners', 'public');
            $data['background_image'] = $imagePath;
        }

        HeroSection::create($data);

        return redirect()->route('mitra.hero_sections.index')->with('success', 'Hero Section created successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(HeroSection $heroSection)
    {
        return view('dashboard-mitra.hero_sections.edit', compact('heroSection'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, HeroSection $heroSection)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'button_text' => 'nullable|string|max:255',
            'button_url' => 'nullable|string|max:255',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $data = $request->except('background_image');

        if ($request->hasFile('background_image')) {
            // Hapus gambar lama jika ada
            if ($heroSection->background_image) {
                Storage::disk('public')->delete($heroSection->background_image);
            }
            $imagePath = $request->file('background_image')->store('hero_banners', 'public');
            $data['background_image'] = $imagePath;
        }

        $heroSection->update($data);

        return redirect()->route('mitra.hero_sections.index')->with('success', 'Hero Section updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HeroSection $heroSection)
    {
        if ($heroSection->background_image) {
            Storage::disk('public')->delete($heroSection->background_image);
        }
        $heroSection->delete();

        return redirect()->route('mitra.hero_sections.index')->with('success', 'Hero Section deleted successfully!');
    }
}