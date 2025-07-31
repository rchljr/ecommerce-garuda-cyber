<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubscriptionPackage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SubscriptionPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Kosongkan tabel sebelum seeding untuk menghindari duplikat
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        SubscriptionPackage::truncate();
        DB::table('subscription_package_features')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Data paket yang akan di-seed
        $packages = [
            [
                'package_name' => 'Starter Plan',
                'description' => 'Solusi ideal untuk memulai dan mengembangkan bisnis online Anda dengan fitur esensial.',
                'monthly_price' => 150000,
                'yearly_price' => 1500000, // Asumsi diskon, misal bayar 10 bulan untuk 1 tahun
                'discount_year' => 17, // (150rb*12 - 1.5jt) / (150rb*12) * 100%
                'is_trial' => true,
                'trial_days' => 14,
                'features' => [
                    '1 Tema menarik yang dapat dikustom',
                    'Metode pembayaran fleksibel',
                    'Manajemen penyimpanan',
                    'Alamat toko unik (contoh: ecommercegaruda.my.id/tenant/nama-toko-anda)',
                ],
            ],
            [
                'package_name' => 'Business Plan',
                'description' => 'Tingkatkan skala bisnis Anda dengan fitur canggih dan pilihan tema yang lebih eksklusif.',
                'monthly_price' => 300000,
                'yearly_price' => 3000000, // Asumsi diskon, misal bayar 10 bulan untuk 1 tahun
                'discount_year' => 17, // (300rb*12 - 3jt) / (300rb*12) * 100%
                'is_trial' => false,
                'trial_days' => 0,
                'features' => [
                    'Toko online lengkap dengan 3 pilihan tema yang dapat digunakan',
                    'Metode pembayaran fleksibel',
                    'Manajemen penyimpanan',
                    'Alamat toko unik (contoh: ecommercegaruda.my.id/tenant/nama-toko-anda)',
                ],
            ],
            [
                'package_name' => 'Enterprise Plan',
                'description' => 'Solusi terintegrasi dan dapat disesuaikan sepenuhnya untuk perusahaan skala besar.',
                'monthly_price' => null, // Harga custom, perlu kontak
                'yearly_price' => null,
                'discount_year' => 0,
                'is_trial' => false,
                'trial_days' => 0,
                'features' => [
                    'Tema dan Fitur Kustom Sesuai Kebutuhan',
                    'Metode pembayaran fleksibel',
                    'Manajemen penyimpanan',
                    'Dukungan Prioritas & Konsultasi',
                    'Dan masih banyak lagi',
                ],
            ],
        ];

        // Looping untuk membuat setiap paket dan fiturnya
        foreach ($packages as $pkgData) {
            $package = SubscriptionPackage::create([
                'id' => (string) Str::uuid(),
                'package_name' => $pkgData['package_name'],
                'description' => $pkgData['description'],
                'monthly_price' => $pkgData['monthly_price'],
                'yearly_price' => $pkgData['yearly_price'],
                'discount_year' => $pkgData['discount_year'],
                'is_trial' => $pkgData['is_trial'],
                'trial_days' => $pkgData['trial_days'],
            ]);

            foreach ($pkgData['features'] as $featureName) {
                $package->features()->create([
                    'id' => (string) Str::uuid(),
                    'feature' => $featureName,
                ]);
            }
        }
    }
}