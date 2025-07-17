<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\CustomTema;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class TemaController extends Controller
{
    /**
     * Menampilkan form untuk membuat atau mengedit tema.
     */

    public function index()
    {
        $templates = Template::all();
        $tenant = Auth::user()->tenant;

        // PERBAIKAN: Pastikan variabel selalu didefinisikan.
        $currentTemplateId = null; // Beri nilai default

        // Hanya ambil template_id jika tenant ada
        if ($tenant) {
            $currentTemplateId = $tenant->template_id;
        } else {
            // Anda bisa menambahkan log atau pesan error di sini jika diperlukan
            // Untuk saat ini, kita biarkan null agar halaman tidak error.
        }

        return view('dashboard-mitra.tema', compact('templates', 'currentTemplateId'));
    }

    public function editor(Template $template)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        // 1. Langsung update template yang dipilih oleh mitra
        $tenant->template_id = $template->id;
        $tenant->save();

        // 2. Siapkan data yang dibutuhkan oleh editor
        $shop = $user->shop;
        if (!$shop || !$shop->subdomain) {
            abort(404, 'Konfigurasi akun mitra tidak lengkap (shop/subdomain tidak ditemukan).');
        }

        $tema = CustomTema::firstOrNew(['user_id' => $user->id]);

        // 3. Tampilkan view editor dua panel
        return view('dashboard-mitra.editor.index', compact('tema', 'shop'));
    }

    public function create()
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        // INI BAGIAN PENTINGNYA: Pastikan baris ini ada
        $shop = $user->shop;

        // Pengecekan untuk memastikan semua data ada
        if (!$tenant || !$shop || !$shop->subdomain) {
            abort(404, 'Konfigurasi akun mitra tidak lengkap (tenant/shop/subdomain tidak ditemukan).');
        }

        // Ambil data tema yang sudah ada jika ada
        $tema = CustomTema::firstOrNew(['user_id' => $user->id]);

        // Ambil semua template yang tersedia dari database
        $templates = Template::all();

        // Pastikan variabel $shop dikirim ke view menggunakan compact()
        return view('dashboard-mitra.tema.create', compact('tema', 'templates', 'tenant', 'shop'));
    }


    /**
     * Menyimpan data tema ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'template_name'    => 'required|string',
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
        // dd($data);

        // Gunakan updateOrCreate untuk membuat baru atau update yang sudah ada
        CustomTema::updateOrCreate(
            ['user_id' => Auth::id()], // Kondisi pencarian
            $data                      // Data untuk disimpan/diupdate
        );

        return redirect()->back()->with('success', 'Tema berhasil disimpan!');
    }

    public function setTemplate(Request $request)
    {
        $request->validate([
            'template_id' => 'required|exists:templates,id',
        ]);

        $user = Auth::user();
        $tenant = $user->tenant;

        if ($tenant) {
            // Update template_id di tabel tenants
            $tenant->template_id = $request->template_id;
            $tenant->save();

            return response()->json(['success' => true, 'message' => 'Template berhasil diubah.']);
        }

        return response()->json(['success' => false, 'message' => 'Gagal mengubah template.'], 400);
    }

    public function updateTheme(Request $request)
    {
        $request->validate([
            'template_id' => 'required|exists:templates,id',
        ]);

        $user = Auth::user();
        $tenant = $user->tenant;

        if ($tenant) {
            $tenant->template_id = $request->template_id;
            $tenant->save();

            return redirect()->back()->with('success', 'Template berhasil diperbarui.');
        }

        return redirect()->back()->with('error', 'Gagal mengubah template.');
    }
}
