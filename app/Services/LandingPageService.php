<?php

namespace App\Services;

use App\Models\User;
use App\Models\Product;
use App\Models\LandingPage;
use Illuminate\Http\Request;

class LandingPageService
{
    /**
     * Mengambil baris statistik dari database.
     */
    public function getStatistics(): LandingPage
    {
        return LandingPage::firstOrCreate([], [
            'total_users' => 12345,
            'total_shops' => 12345,
            'total_visitors' => 12345,
            'total_transactions' => 12345,
        ]);
    }

    /**
     * Memperbarui data statistik.
     */
    public function updateStatistics(array $data): LandingPage
    {
        $landingPage = $this->getStatistics();
        $landingPage->update($data);
        return $landingPage;
    }

    /**
     * Mengambil 3 mitra terbaru untuk ditampilkan di halaman utama.
     */
    public function getPartners()
    {
        return User::role('mitra')
            ->whereHas('shop')
            ->whereHas('userPackage', fn($q) => $q->where('status', 'active'))
            ->whereHas('subdomain', fn($q) => $q->where('publication_status', 'published'))
            ->with(['shop', 'subdomain'])
            ->latest()
            ->take(3)
            ->get();
    }

    /**
     * Mengambil SEMUA mitra dengan filter dan paginasi untuk halaman jelajah.
     */
    public function getAllPartners(Request $request)
    {
        $query = User::role('mitra')
            ->whereHas('shop')
            ->whereHas('subdomain')
            // Eager load relasi yang dibutuhkan, termasuk voucher yang aktif
            ->whereHas('userPackage', fn($q) => $q->where('status', 'active'))
            ->whereHas('subdomain', fn($q) => $q->where('publication_status', 'published'))
            ->with(['shop', 'subdomain', 'activeVouchers']);

        // Filter berdasarkan pencarian nama toko
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('shop', function ($q) use ($search) {
                $q->where('shop_name', 'like', '%' . $search . '%');
            });
        }

        // Filter berdasarkan kategori produk
        if ($request->filled('category')) {
            $categorySlug = $request->input('category');
            $query->whereHas('shop', function ($q) use ($categorySlug) {
                $q->where('product_categories', 'like', '%' . $categorySlug . '%');
            });
        }

        return $query->latest()->paginate(12); // Paginasi 12 item per halaman
    }

    /**
     * Mengambil ID semua mitra yang tokonya terlihat oleh publik.
     * @return \Illuminate\Support\Collection
     */
    private function getVisiblePartnerIds()
    {
        return User::role('mitra')
            ->whereHas('userPackage', fn($q) => $q->where('status', 'active'))
            ->whereHas('subdomain', fn($q) => $q->where('publication_status', 'published'))
            ->pluck('id');
    }

    /**
     *  Mengambil produk unggulan dari semua mitra yang terlihat.
     * @param string $type ('best_seller', 'new_arrival', 'hot_sale')
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getFeaturedProducts(string $type, int $limit = 8)
    {
        $partnerIds = $this->getVisiblePartnerIds();

        if ($partnerIds->isEmpty()) {
            return collect();
        }

        // Tentukan nama kolom boolean berdasarkan tipe
        $column = 'is_' . $type;

        // Ambil produk dengan relasi yang dibutuhkan untuk membuat link
        return Product::whereIn('user_id', $partnerIds)
            ->where($column, true)
            ->where('status', 'active')
            ->with(['shopOwner.shop', 'shopOwner.subdomain']) // Eager load untuk URL
            ->latest()
            ->take($limit)
            ->get();
    }
}
