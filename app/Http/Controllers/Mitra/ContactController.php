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
     * Menampilkan halaman kontak publik.
     * @return \Illuminate->View->View
     */
    public function showPublic()
    {
        // Ambil entry kontak pertama untuk ditampilkan di halaman publik
        $contact = Contact::first(); // Jika tidak ada, mungkin tampilkan pesan default
        return view('template1.contact', compact('contact'));
    }
}
