<?php

namespace App\Http\Controllers;

use App\Models\Template;
use App\Models\Category; // <-- Import Category
use App\Models\Testimoni;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\SubscriptionPackage;
use App\Services\LandingPageService;
use App\Services\SubscriptionPackageService;

class LandingPageController extends BaseController
{
    protected $landingPageService;
    protected $subscriptionPackageService;

    public function __construct(LandingPageService $landingPageService, SubscriptionPackageService $subscriptionPackageService)
    {
        $this->landingPageService = $landingPageService;
        $this->subscriptionPackageService = $subscriptionPackageService;
    }

    public function home()
    {
        $stats = $this->landingPageService->getStatistics();
        $testimonials = Testimoni::where('status', 'published')->latest()->take(10)->get();
        $packages = $this->subscriptionPackageService->getAllPackages();
        $sortedPackages = $packages->sortBy(function ($package) {
            if ($package->is_trial)
                return 0;
            if (is_null($package->monthly_price))
                return 2;
            return 1;
        });
        $templates = Template::all();
        $partners = $this->landingPageService->getPartners();

        return view('landing-page.index', [
            'stats' => $stats,
            'testimonials' => $testimonials,
            'packages' => $sortedPackages,
            'templates' => $templates,
            'partners' => $partners,
        ]);
    }

    /**
     * Menampilkan halaman untuk menjelajahi semua mitra dengan filter.
     */
    public function allTenants(Request $request)
    {
        // Ambil semua kategori untuk dropdown filter
        $categories = Category::orderBy('name')->get();

        // Ambil semua mitra dengan filter dan paginasi dari service
        $partners = $this->landingPageService->getAllPartners($request);

        return view('landing-page.all-tenants', compact('partners', 'categories'));
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
        $allowedKeys = ['total_users', 'total_shops', 'total_visitors', 'total_transactions'];

        $validated = $request->validate([
            'stat_key' => ['required', 'string', Rule::in($allowedKeys)],
            'stat_value' => 'required|integer|min:0',
        ]);

        $data = [
            $validated['stat_key'] => $validated['stat_value'],
        ];

        $this->landingPageService->updateStatistics($data);

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
