<?php

namespace App\Services;

use App\Models\LandingPage;

class LandingPageService
{
    /**
     * Mengambil baris statistik dari database.
     * Jika tidak ada, buat satu baris default.
     */
    public function getStatistics(): LandingPage
    {
        // Menggunakan firstOrCreate untuk kode yang lebih ringkas
        return LandingPage::firstOrCreate(
            [], // Kondisi pencarian (kosong berarti selalu mencari yang pertama)
            [   // Nilai default jika tidak ditemukan dan perlu dibuat
                'total_users' => 0,
                'total_shops' => 0,
                'total_visitors' => 0,
                'total_transactions' => 0,
            ]
        );
    }

    /**
     * Memperbarui data statistik.
     */
    public function updateStatistics(array $data): LandingPage
    {
        $landingPage = $this->getStatistics();

        // Metode update() hanya akan memperbarui kolom yang ada di dalam $data.
        // Tidak perlu memeriksa setiap kolom secara manual.
        $landingPage->update($data);

        return $landingPage;
    }
}
