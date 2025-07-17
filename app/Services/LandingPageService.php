<?php

namespace App\Services;

use App\Models\User;
use App\Models\LandingPage;
use Illuminate\Http\Request; // <-- Import Request

class LandingPageService
{
    /**
     * Mengambil baris statistik dari database.
     */
    public function getStatistics(): LandingPage
    {
        return LandingPage::firstOrCreate([], [
            'total_users' => 0,
            'total_shops' => 0,
            'total_visitors' => 0,
            'total_transactions' => 0,
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
     * Mengambil 8 mitra terbaru untuk ditampilkan di halaman utama.
     */
    public function getPartners()
    {
        return User::role('mitra')
            ->whereHas('shop')
            ->whereHas('subdomain', fn($q) => $q->where('status', 'active'))
            ->with(['shop', 'subdomain'])
            ->latest()
            ->take(8)
            ->get();
    }

    /**
     * Mengambil SEMUA mitra dengan filter dan paginasi untuk halaman jelajah.
     */
    public function getAllPartners(Request $request)
    {
        $query = User::role('mitra')
            ->whereHas('shop')
            ->whereHas('subdomain', fn($q) => $q->where('status', 'active'))
            // Eager load relasi yang dibutuhkan, termasuk voucher yang aktif
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
                // Menggunakan 'like' agar lebih fleksibel.
                // Ini akan cocok jika slug kategori ada di dalam kolom 'product_categories',
                // baik itu sebagai nilai tunggal ("fashion") atau bagian dari daftar ("fashion,makanan").
                $q->where('product_categories', 'like', '%' . $categorySlug . '%');
            });
        }

        return $query->latest()->paginate(12); // Paginasi 12 item per halaman
    }
}
