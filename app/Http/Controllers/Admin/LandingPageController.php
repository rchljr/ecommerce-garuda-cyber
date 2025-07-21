<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use App\Models\Template;
use App\Models\Testimoni;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\SubscriptionPackage;
use App\Services\LandingPageService;
use App\Services\TestimonialService;
use App\Http\Controllers\BaseController;
use App\Services\SubscriptionPackageService;

class LandingPageController extends BaseController
{
    protected $landingPageService;
    protected $subscriptionPackageService;

    public function __construct(LandingPageService $landingPageService, SubscriptionPackageService $subscriptionPackageService, TestimonialService $testimonialService)
    {
        $this->landingPageService = $landingPageService;
        $this->subscriptionPackageService = $subscriptionPackageService;
        $this->testimonialService = $testimonialService;
    }

    public function home()
    {
        $stats = $this->landingPageService->getStatistics();
        $testimonials = $this->testimonialService->getPublishedLandingPageTestimonials();
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
        // Mengambil data mitra dari service
        $partners = $this->landingPageService->getAllPartners($request);

        // Mengambil data kategori untuk filter
        $categories = Category::all();

        // Mengambil data produk unggulan dari semua toko menggunakan service
        $bestSellers = $this->landingPageService->getFeaturedProducts('best_seller');
        $newArrivals = $this->landingPageService->getFeaturedProducts('new_arrival');
        $hotSales = $this->landingPageService->getFeaturedProducts('hot_sale');

        return view('landing-page.all-tenants', [
            'partners' => $partners,
            'categories' => $categories,
            'bestSellers' => $bestSellers,
            'newArrivals' => $newArrivals,
            'hotSales' => $hotSales,
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
