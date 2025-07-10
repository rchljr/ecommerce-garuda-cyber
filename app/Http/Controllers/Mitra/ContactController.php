<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\Contact; // Pastikan mengarah ke model tenant
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    /**
     * Menampilkan form untuk mengedit informasi kontak milik tenant.
     * Ini adalah untuk dashboard mitra.
     */
    public function edit()
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        if (!$tenant) {
            abort(403, 'Tenant tidak ditemukan.');
        }

        // Ambil data kontak dari dalam database tenant
        $contact = $tenant->execute(function () {
            // Cari entry kontak pertama, jika tidak ada, buat instance baru untuk diisi di form
            return Contact::firstOrNew([]);
        });
        
        return view('dashboard-mitra.contacts.edit', compact('contact'));
    }

    /**
     * Memperbarui informasi kontak di dalam database tenant.
     * Ini adalah untuk dashboard mitra.
     */
    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'address_line1' => 'nullable|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'facebook_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'pinterest_url' => 'nullable|url|max:255',
            'map_embed_code' => 'nullable|string',
            'working_hours' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $tenant = $user->tenant;

        if (!$tenant) {
            return back()->with('error', 'Tenant tidak ditemukan.');
        }

        // Jalankan operasi update kontak di dalam database tenant
        $tenant->execute(function () use ($validatedData) {
            // Gunakan updateOrCreate untuk membuat baru atau update yang sudah ada
            // Kita asumsikan hanya ada satu baris kontak per tenant
            Contact::updateOrCreate(
                ['id' => 1], // Selalu targetkan baris pertama
                $validatedData
            );
        });

        return redirect()->route('mitra.contacts.edit')->with('success', 'Informasi kontak berhasil diperbarui!');
    }

    /**
     * Menampilkan halaman kontak publik untuk tenant.
     * Method ini sudah benar karena ia berjalan di rute tenant,
     * di mana middleware Spatie sudah mengganti koneksi database.
     */
    public function showPublic(Request $request)
    {
        // Middleware Spatie sudah mengidentifikasi tenant dan mengganti koneksi.
        // Jadi, kita bisa langsung query model Contact.
        $contact = Contact::first();

        // Ambil data tenant dan path template yang sudah disiapkan oleh middleware
        $tenant = $request->get('tenant'); // Helper dari Spatie untuk mendapatkan tenant saat ini
        $templatePath = optional($tenant->template)->path ?? 'default'; // Fallback jika template tidak ada

        return view($templatePath . '.contact', compact('tenant', 'contact'));
    }
}
