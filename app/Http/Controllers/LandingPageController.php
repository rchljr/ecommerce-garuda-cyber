<?php

namespace App\Http\Controllers;

use App\Models\Template;
use App\Models\Testimoni;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\SubscriptionPackage;
use App\Services\LandingPageService;
use App\Services\SubscriptionPackageService;

class LandingPageController extends BaseController
{
    protected $landingPageService;
    protected $subscriptionPackageService; // 2. Tambahkan properti

    // 3. Inject kedua service di constructor
    public function __construct(LandingPageService $landingPageService, SubscriptionPackageService $subscriptionPackageService)
    {
        $this->landingPageService = $landingPageService;
        $this->subscriptionPackageService = $subscriptionPackageService;
    }

    public function home()
    {
        // Ambil data statistik
        $stats = $this->landingPageService->getStatistics();

        // Ambil data testimoni
        $testimonials = Testimoni::where('status', 'published')->latest()->take(10)->get();

        // 4. Ambil paket menggunakan service agar relasi 'features' ikut terbawa
        $packages = $this->subscriptionPackageService->getAllPackages();

        // Urutkan paket seperti di halaman registrasi
        $sortedPackages = $packages->sortBy(function ($package) {
            if ($package->is_trial)
                return 0;
            if (is_null($package->monthly_price))
                return 2;
            return 1;
        });

        // Ambil Template
        $templates = Template::all();

        // 5. Kirim semua data yang dibutuhkan ke view
        return view('landing-page.index', [
            'stats' => $stats,
            'testimonials' => $testimonials,
            'packages' => $sortedPackages,
            'templates' => $templates,
        ]);
    }
    /**
     * Menampilkan halaman kelola statistik.
     */
    public function adminLanding()
    {
        $stats = $this->landingPageService->getStatistics();
        return view('dashboard-admin.kelola-landing', compact('stats'));
    }

    /**
     * Memperbarui nilai statistik.
     */
    public function update(Request $request)
    {
        // Daftar kolom yang diizinkan untuk diupdate
        $allowedKeys = ['total_users', 'total_shops', 'total_visitors', 'total_transactions'];

        // Validasi input berdasarkan kunci (nama kolom), bukan nama tampilan
        $validated = $request->validate([
            'stat_key' => ['required', 'string', Rule::in($allowedKeys)],
            'stat_value' => 'required|integer|min:0',
        ]);

        // Siapkan data untuk service
        $data = [
            $validated['stat_key'] => $validated['stat_value'],
        ];

        $this->landingPageService->updateStatistics($data);

        // Mapping kunci ke nama tampilan untuk pesan sukses yang lebih ramah
        $keyToNameMap = [
            'total_users' => 'Total Pengguna',
            'total_shops' => 'Total Toko',
            'total_visitors' => 'Total Pengunjung',
            'total_transactions' => 'Total Transaksi',
        ];

        $statName = $keyToNameMap[$validated['stat_key']] ?? 'Statistik';

        return back()->with('success', "Statistik \"$statName\" berhasil diperbarui.");
    }
}
