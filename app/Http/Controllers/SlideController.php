<?php

namespace App\Http\Controllers;

use App\Models\Slide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Untuk upload/hapus gambar

class SlideController extends Controller
{
    /**
     * Menampilkan daftar slide.
     */
    public function index()
    {
        $slides = Slide::orderBy('order')->get();
        // Karena ini akan dimuat via AJAX, kita langsung return partial view
        return view('dashboard-mitra.slides.index_partial', compact('slides'));
    }

    /**
     * Menampilkan form untuk membuat slide baru.
     */
    public function create()
    {
        // Ini bisa jadi modal atau halaman terpisah, kita buatkan partial form
        return view('dashboard-mitra.slides.create_edit_form_partial');
    }

    /**
     * Menyimpan slide baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'text_position' => 'nullable|in:left,center,right',
            'text_color' => 'nullable|string|max:50', // Misal 'white', '#FF0000'
            'button_text' => 'nullable|string|max:100',
            'button_url' => 'nullable|url',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('slides', 'public');
            // 'slides' adalah folder di dalam storage/app/public
        }

        Slide::create([
            'image_path' => $imagePath,
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'text_position' => $request->input('text_position'),
            'text_color' => $request->input('text_color'),
            'button_text' => $request->input('button_text'),
            'button_url' => $request->input('button_url'),
            'order' => $request->input('order', 0),
            'is_active' => $request->boolean('is_active'),
        ]);

        // Karena ini AJAX, kembalikan JSON response
        return response()->json(['success' => true, 'message' => 'Slide berhasil ditambahkan.']);
    }

    /**
     * Menampilkan form untuk mengedit slide yang ada.
     */
    public function edit(Slide $slide)
    {
        return view('dashboard-mitra.slides.create_edit_form_partial', compact('slide'));
    }

    /**
     * Memperbarui slide yang ada di database.
     */
    public function update(Request $request, Slide $slide)
    {
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Nullable karena bisa tidak diubah
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'text_position' => 'nullable|in:left,center,right',
            'text_color' => 'nullable|string|max:50',
            'button_text' => 'nullable|string|max:100',
            'button_url' => 'nullable|url',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $data = $request->except('image'); // Ambil semua data kecuali 'image'

        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($slide->image_path && Storage::disk('public')->exists($slide->image_path)) {
                Storage::disk('public')->delete($slide->image_path);
            }
            $data['image_path'] = $request->file('image')->store('slides', 'public');
        }

        $slide->update($data);

        return response()->json(['success' => true, 'message' => 'Slide berhasil diperbarui.']);
    }

    /**
     * Menghapus slide dari database.
     */
    public function destroy(Slide $slide)
    {
        // Hapus gambar dari storage
        if ($slide->image_path && Storage::disk('public')->exists($slide->image_path)) {
            Storage::disk('public')->delete($slide->image_path);
        }

        $slide->delete();

        return response()->json(['success' => true, 'message' => 'Slide berhasil dihapus.']);
    }
}