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

        // Diarahkan kembali ke halaman edit, bukan update.
        return redirect()->route('mitra.contacts.update')->with('success', 'Informasi kontak berhasil diperbarui!');
    }

    /**
     * Menampilkan halaman kontak publik untuk tenant.
     * Method ini sudah disesuaikan untuk memastikan peta tampil.
     */
    public function showPublic(Request $request)
    {
        // Middleware sudah mengidentifikasi tenant dan mengganti koneksi.
        $contact = Contact::first();
        
        $tenant = $request->get('tenant');
        $templatePath = optional($tenant->template)->path ?? 'default';

        $mapEmbedCode = $contact ? $contact->map_embed_code : null;

        // ## PERBAIKAN DI SINI ##
        // Buat URL Google Maps dari data alamat yang ada.
        $googleMapsLink = '#'; // Default link jika tidak ada alamat
        if ($contact && $contact->address_line1) {
            // Gabungkan alamat menjadi satu string untuk query
            $addressQuery = urlencode(
                $contact->address_line1 . ', ' . 
                $contact->city . ', ' . 
                $contact->state . ' ' . 
                $contact->postal_code
            );
            $googleMapsLink = "https://www.google.com/maps/search/?api=1&query=" . $addressQuery;
        }

        // Kirim semua variabel yang dibutuhkan ke view.
        return view($templatePath . '.contact', compact(
            'tenant', 
            'contact', 
            'mapEmbedCode',
            'googleMapsLink' 
        ));
    }
}
