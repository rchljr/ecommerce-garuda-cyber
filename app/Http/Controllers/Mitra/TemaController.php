<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\CustomTema; // Pastikan mengarah ke model tenant
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TemaController extends Controller
{
    /**
     * Menampilkan form untuk membuat atau mengedit tema.
     */
    public function create()
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        if (!$tenant) {
            abort(403, 'Tenant tidak ditemukan.');
        }

        // Ambil data tema dari dalam database tenant
        $tema = $tenant->execute(function () use ($user) {
            // Ambil tema yang sudah ada untuk user ini, atau buat instance baru
            return CustomTema::firstOrNew(['user_id' => $user->id]);
        });
        
        // Daftar template yang tersedia (ini bisa tetap di sini atau dipindahkan ke config)
        $templates = [
            'template1' => 'Sleek',
            'template2' => 'Vibrant',
            'template3' => 'Refined',
        ];

        return view('dashboard-mitra.tema', compact('tema', 'templates'));
    }

    /**
     * Menyimpan data tema ke database tenant.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'template_name'    => 'required|string|in:template1,template2,template3',
            'shop_name'        => 'required|string|max:255',
            'shop_description' => 'nullable|string',
            'shop_logo'        => 'nullable|image|mimes:jpg,jpeg,png|max:2048', 
            'primary_color'    => 'required|string|max:7', 
            'secondary_color'  => 'required|string|max:7',
        ]);

        $user = Auth::user();
        $tenant = $user->tenant;

        if (!$tenant) {
            return back()->with('error', 'Tenant tidak ditemukan.');
        }

        // Jalankan operasi simpan tema di dalam database tenant
        $tenant->execute(function () use ($validatedData, $request, $user) {
            $data = $validatedData;
            $data['user_id'] = $user->id;
            $data['subdomain_id'] = $user->subdomain->id; // Simpan juga referensi subdomain

            // Ambil tema yang sudah ada untuk diupdate
            $existingTema = CustomTema::where('user_id', $user->id)->first();

            // Handle upload logo jika ada
            if ($request->hasFile('shop_logo')) {
                // Hapus logo lama jika ada
                if ($existingTema && $existingTema->shop_logo) {
                    Storage::disk('public')->delete($existingTema->shop_logo);
                }
                $data['shop_logo'] = $request->file('shop_logo')->store('shop-logos', 'public');
            }

            // Gunakan updateOrCreate untuk membuat baru atau update yang sudah ada
            CustomTema::updateOrCreate(
                ['user_id' => $user->id], // Kondisi pencarian
                $data                    // Data untuk disimpan/diupdate
            );
        });

        return redirect()->back()->with('success', 'Tema berhasil disimpan!');
    }
}
