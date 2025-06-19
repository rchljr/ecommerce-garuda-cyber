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
                'name' => 'Starter Plan',
                'description' => 'Untuk kebutuhan bisnis sederhana Anda',
                'monthly_price' => 150000,
                'yearly_price' => 1500000, // Asumsi diskon, misal bayar 10 bulan untuk 1 tahun
                'discount_year' => 17, // (150rb*12 - 1.5jt) / (150rb*12) * 100%
                'is_trial' => true,
                'trial_days' => 14,
                'features' => [
                    'Toko online lengkap yang bisa dikustom sesuai yang diinginkan',
                    'Metode pembayaran fleksibel',
                    'Manajemen penyimpanan',
                    'Gratis subdomain (e.g., your-store.garuda.com)',
                ],
            ],
            [
                'name' => 'Business Plan',
                'description' => 'Untuk bisnis menengah dan besar dengan kebutuhan lanjutan',
                'monthly_price' => 300000,
                'yearly_price' => 3000000, // Asumsi diskon, misal bayar 10 bulan untuk 1 tahun
                'discount_year' => 17, // (300rb*12 - 3jt) / (300rb*12) * 100%
                'is_trial' => false,
                'trial_days' => 0,
                'features' => [
                    'Toko online lengkap dengan 5 pilihan tema yang dapat digunakan',
                    'Metode pembayaran fleksibel',
                    'Manajemen penyimpanan',
                    'Gratis subdomain (e.g., your-store.garuda.com)',
                ],
            ],
            [
                'name' => 'Enterprise Plan',
                'description' => 'A custom solution for large companies with advanced e-commerce requirements.',
                'monthly_price' => null, // Harga custom, perlu kontak
                'yearly_price' => null,
                'discount_year' => 0,
                'is_trial' => false,
                'trial_days' => 0,
                'features' => [
                    'Toko online lengkap dengan unlimited tema',
                    'Metode pembayaran fleksibel',
                    'Manajemen penyimpanan',
                    'Gratis subdomain (e.g., your-store.garuda.com)',
                    'Dan masih banyak lagi',
                ],
            ],
        ];

        // Looping untuk membuat setiap paket dan fiturnya
        foreach ($packages as $pkgData) {
            $package = SubscriptionPackage::create([
                'id' => (string) Str::uuid(),
                'package_name' => $pkgData['name'],
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