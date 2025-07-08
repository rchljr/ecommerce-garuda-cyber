<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\CustomTema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TemaController extends Controller
{
    /**
     * Menampilkan form untuk membuat atau mengedit tema.
     */
    public function create()
    {
        // Ambil data tema yang sudah ada untuk user ini, atau buat instance baru
        $tema = CustomTema::firstOrNew(['user_id' => Auth::id()]);

        // Daftar template yang tersedia
        $templates = [
            'template1' => 'Sleek',
            'template2' => 'Vibrant',
            'template3' => 'Refined',
        ];

        return view('dashboard-mitra.tema', compact('tema', 'templates'));
    }

    /**
     * Menyimpan data tema ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'template_name'    => 'required|string|in:template1,template2,template3',
            'shop_name'        => 'required|string|max:255',
            'shop_description' => 'nullable|string',
            'shop_logo'        => 'nullable|image|mimes:jpg,jpeg,png|max:2048', 
            'primary_color'    => 'required|string|max:7', 
            'secondary_color'  => 'required|string|max:7',
        ]);

        $data = $request->except('shop_logo');
        $data['user_id'] = Auth::id();

        // Handle upload logo jika ada
        if ($request->hasFile('shop_logo')) {
            $data['shop_logo'] = $request->file('shop_logo')->store('shop-logos', 'public');
        }

        // Gunakan updateOrCreate untuk membuat baru atau update yang sudah ada
        CustomTema::updateOrCreate(
            ['user_id' => Auth::id()], // Kondisi pencarian
            $data                      // Data untuk disimpan/diupdate
        );

        return redirect()->back()->with('success', 'Tema berhasil disimpan!');
    }
}