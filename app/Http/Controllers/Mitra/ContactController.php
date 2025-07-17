<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\Contact; // Import model Contact
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session; // Untuk pesan session

class ContactController extends Controller
{
    /**
     * Menampilkan form untuk mengedit informasi kontak (asumsi ID 1 atau yang pertama).
     * Jika belum ada, akan membuat entri kosong.
     * @return \Illuminate->View->View
     */
    public function edit()
    {
        // Cari entry kontak pertama, jika tidak ada, buat baru
        $contact = Contact::firstOrCreate([]);
        return view('dashboard-mitra.contacts.edit', compact('contact'));
    }

    /**
     * Memperbarui informasi kontak.
     * @param Request $request
     * @return \Illuminate->Http->RedirectResponse
     */
    public function update(Request $request)
    {
        $contact = Contact::firstOrCreate([]); // Ambil atau buat entri

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
            'map_embed_code' => 'nullable|string', // Kode HTML/iframe
            'working_hours' => 'nullable|string|max:500',
        ]);

        $contact->update($validatedData);

        return redirect()->route('mitra.contacts')->with('success', 'Informasi kontak berhasil diperbarui!');
    }

    /**
     * Menampilkan halaman kontak publik untuk tenant.
     */

    public function showPublic(Request $request)
    {
        // 1. Ambil data tenant dan path template dari middleware
        $tenant = $request->get('tenant');
        $templatePath = $tenant->template->path;

        // 2. Ambil data kontak dari database
        $contact = Contact::first();

        // 3. Siapkan variabel dari data kontak.
        //    Gunakan null coalescing operator (??) untuk keamanan jika $contact tidak ada (null).
        $mapEmbedCode = $contact->map_embed_code ?? null;
        $googleMapsLink = $contact->google_maps_link ?? '#'; // Asumsi nama kolomnya google_maps_link

        // 4. Kirim semua variabel yang dibutuhkan ke view
        return view($templatePath . '.contact', [
            'tenant'         => $tenant,
            'contact'        => $contact,
            'mapEmbedCode'   => $mapEmbedCode,
            'googleMapsLink' => $googleMapsLink,
        ]);
    }
}
