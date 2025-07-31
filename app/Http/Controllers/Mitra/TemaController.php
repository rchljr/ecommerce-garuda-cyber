<?php

namespace App\Http\Controllers\Mitra;

use App\Models\Template;
use App\Models\CustomTema;
use App\Models\UserPackage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class TemaController extends Controller
{
    /**
     * Menampilkan form untuk membuat atau mengedit tema.
     */

    public function index()
    {
        $user = Auth::user();
        $templates = Template::orderBy('id', 'asc')->get();
        $tenant = $user->tenant;
        $currentTemplateId = $tenant ? $tenant->template_id : null;

        // [LOGIKA BARU] Pengecekan paket berlangganan mitra
        $activePackage = UserPackage::where('user_id', $user->id)
            ->with('subscriptionPackage') // Eager load untuk mendapatkan nama paket
            ->first();

        $isStarterPlan = false;
        // Pastikan relasi ada sebelum mengakses propertinya
        if ($activePackage && $activePackage->subscriptionPackage && $activePackage->subscriptionPackage->name === 'Starter Plan') {
            $isStarterPlan = true;
        }

        // Menggunakan nama view yang sesuai dengan file yang Anda berikan
        return view('dashboard-mitra.tema', compact(
            'templates',
            'currentTemplateId',
            'isStarterPlan' // Kirim variabel ini ke view
        ));
    }

    // public function index()
    // {
    //     $templates = Template::all();
    //     $tenant = Auth::user()->tenant;

    //     // PERBAIKAN: Pastikan variabel selalu didefinisikan.
    //     $currentTemplateId = null; // Beri nilai default

    //     // Hanya ambil template_id jika tenant ada
    //     if ($tenant) {
    //         $currentTemplateId = $tenant->template_id;
    //     } else {
    //         // Anda bisa menambahkan log atau pesan error di sini jika diperlukan
    //         // Untuk saat ini, kita biarkan null agar halaman tidak error.
    //     }

    //     return view('dashboard-mitra.tema', compact('templates', 'currentTemplateId'));
    // }

    public function editor(Template $template)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        // 1. Langsung update template yang dipilih oleh mitra
        if ($tenant) {
            $tenant->template_id = $template->id;
            $tenant->save();
        }

        // 2. Siapkan data yang dibutuhkan oleh editor
        $shop = $user->shop;
        if (!$shop || !$shop->subdomain) {
            abort(404, 'Konfigurasi akun mitra tidak lengkap (shop/subdomain tidak ditemukan).');
        }

        // [MODIFIKASI] Mengambil data tema berdasarkan user_id DAN subdomain_id
        $tema = CustomTema::firstOrNew([
            'user_id' => $user->id,
            'subdomain_id' => $tenant->subdomain_id
        ]);

        // 3. Tampilkan view editor dua panel
        return view('dashboard-mitra.editor.index', compact('tema', 'shop', 'tenant'));
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
            'template_name' => 'required|string',
            'shop_name' => 'required|string|max:255',
            'shop_description' => 'nullable|string',
            'shop_logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'primary_color' => 'required|string|max:7',
            'secondary_color' => 'required|string|max:7',
        ]);

        $user = Auth::user()->load('tenant'); // Eager load relasi tenant

        // [FIX] Pastikan tenant ada sebelum melanjutkan
        if (!$user->tenant || !$user->tenant->subdomain_id) {
            return redirect()->back()->with('error', 'Gagal menyimpan tema. Konfigurasi subdomain tidak ditemukan.');
        }

        $data = $request->except('shop_logo');
        $data['user_id'] = $user->id;
        $data['subdomain_id'] = $user->tenant->subdomain_id; // [FIX] Tambahkan subdomain_id

        // Handle upload logo jika ada
        if ($request->hasFile('shop_logo')) {
            $data['shop_logo'] = $request->file('shop_logo')->store('shop-logos', 'public');
        }

        // Gunakan updateOrCreate untuk membuat baru atau update yang sudah ada
        CustomTema::updateOrCreate(
            [
                'user_id' => $user->id,
                'subdomain_id' => $user->tenant->subdomain_id // Kondisi pencarian
            ],
            $data // Data untuk disimpan/diupdate
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

    /**
     * Menyimpan template yang dipilih oleh mitra.
     */
    public function updateTheme(Request $request)
    {
        $request->validate([
            'template_id' => 'required|exists:templates,id',
        ]);

        $user = Auth::user();
        $tenant = $user->tenant;
        $chosenTemplate = Template::find($request->template_id);

        // [MODIFIKASI] Validasi di sisi server
        $activePackage = UserPackage::where('user_id', $user->id)->with('subscriptionPackage')->first();
        $isStarterPlan = $activePackage && $activePackage->subscriptionPackage && $activePackage->subscriptionPackage->package_name === 'Starter Plan';

        // Jika pengguna adalah Starter Plan dan mencoba memilih template selain template1
        if ($isStarterPlan && $chosenTemplate->path !== 'template1') {
            return redirect()->back()->with('error', 'Paket Anda tidak mengizinkan penggunaan tema ini. Silakan upgrade paket Anda.');
        }

        if ($tenant) {
            $tenant->template_id = $request->template_id;
            $tenant->save();

            return redirect()->back()->with('success', 'Template berhasil diperbarui.');
        }

        return redirect()->back()->with('error', 'Gagal mengubah template. Data tenant tidak ditemukan.');
    }
}
