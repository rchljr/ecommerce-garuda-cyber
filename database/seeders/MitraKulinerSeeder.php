<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Shop;
use App\Models\Subdomain;
use App\Models\Tenant;
use App\Models\UserPackage;
use App\Models\SubscriptionPackage;
use App\Models\Template;
use App\Models\Contact;
use App\Models\Hero;
use App\Models\Banner;
use App\Models\Product;
use App\Models\SubCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MitraKulinerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // =================================================================
        // BAGIAN 1: PERSIAPAN DATA DI DATABASE PUSAT
        // =================================================================
        $this->command->info('Memulai Seeder untuk Mitra Kuliner Demo...');

        // Data ini diambil dari database pusat
        $businessPackage = SubscriptionPackage::where('package_name', 'Business Plan')->first();
        $template = Template::where('path', 'template2')->first(); // MENGGUNAKAN TEMPLATE 2

        // Mengambil semua sub-kategori kuliner berdasarkan slug
        $subCategorySlugs = ['makanan', 'minuman', 'snack', 'kue-roti', 'lainnya'];
        $subCategories = SubCategory::whereIn('slug', $subCategorySlugs)->get()->keyBy('slug');

        if (!$businessPackage || !$template || $subCategories->count() !== count($subCategorySlugs)) {
            $this->command->error('Pastikan SubscriptionPackage, Template2, dan semua SubCategory Kuliner sudah ada sebelum menjalankan seeder ini.');
            return;
        }

        // Membuat data di database pusat
        $mitra = User::firstOrCreate(['email' => 'mitrakuliner@gmail.com'], ['name' => 'Siti Aminah', 'password' => Hash::make('mitra123'), 'status' => 'active']);
        $mitra->assignRole('mitra');

        $shop = Shop::updateOrCreate(
            ['user_id' => $mitra->id],
            [
                'shop_name' => 'Dapur Lezat Bunda',
                'year_founded' => '2022-01-01',
                'shop_address' => 'Jl. Kuliner No. 45, Pekanbaru',
                'product_categories' => 'kuliner', // KATEGORI UTAMA: KULINER
                'shop_photo' => 'seeders/shop_photo_kuliner.jpg',
                'ktp' => 'seeders/ktp_kuliner.jpg',
            ]
        );
        $subdomain = Subdomain::updateOrCreate(['user_id' => $mitra->id], ['subdomain_name' => 'dapur-lezat-bunda', 'status' => 'active']);
        
        // =================================================================
        // BAGIAN 2: MEMBUAT DATABASE TENANT & MENGISI RECORD TENANT
        // =================================================================
        
        $dbName = 'tenant_' . str_replace('-', '_', $subdomain->subdomain_name);

        try {
            DB::statement("CREATE DATABASE `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $this->command->info("Database '{$dbName}' berhasil dibuat.");
        } catch (\Exception $e) {
            $this->command->warn("Database '{$dbName}' sudah ada, proses pembuatan dilewati.");
        }

        // Membuat data di database pusat
        $tenant = Tenant::updateOrCreate(['user_id' => $mitra->id], ['subdomain_id' => $subdomain->id, 'template_id' => $template->id, 'db_name' => $dbName]);
        UserPackage::updateOrCreate(['user_id' => $mitra->id], ['subs_package_id' => $businessPackage->id, 'plan_type' => 'yearly', 'price_paid' => $businessPackage->yearly_price, 'active_date' => now(), 'expired_date' => now()->addYear(), 'status' => 'active']);

        // =================================================================
        // BAGIAN 3: MENGISI DATA DI DALAM DATABASE TENANT
        // =================================================================
        
        $originalDbName = DB::connection()->getDatabaseName();

        try {
            // PINDAH KONEKSI KE DATABASE TENANT
            config(['database.connections.mysql.database' => $dbName]);
            DB::purge('mysql');
            DB::reconnect('mysql');

            $this->command->info("Berpindah ke database '{$dbName}' untuk mengisi data...");

            // JALANKAN MIGRASI KHUSUS UNTUK TENANT
            $this->command->call('migrate:fresh', [
                '--database' => 'mysql',
                '--path' => 'database/migrations/tenant',
                '--force' => true
            ]);

            // --- SEMUA DATA SPESIFIK TENANT DIBUAT DI SINI ---

            Contact::updateOrCreate(['id' => 1], ['address_line1' => 'Jl. Kuliner No. 45', 'city' => 'Pekanbaru', 'email' => 'info@dapurlezat.com']);
            
            Hero::updateOrCreate(['order' => 1], ['user_id' => $mitra->id, 'title' => 'Menu Spesial Hari Ini', 'subtitle' => 'Cita Rasa Autentik Nusantara', 'image' => 'seeders/hero_kuliner1.jpg', 'is_active' => true]);
            Hero::updateOrCreate(['order' => 2], ['user_id' => $mitra->id, 'title' => 'Nikmati Kesegaran Minuman Kami', 'subtitle' => 'Dibuat dari Bahan Pilihan', 'image' => 'seeders/hero_kuliner2.jpg', 'is_active' => true]);

            Banner::updateOrCreate(['order' => 1], ['user_id' => $mitra->id, 'title' => 'Paket Hemat Keluarga', 'image' => 'seeders/banner_kuliner1.jpg', 'is_active' => true]);
            Banner::updateOrCreate(['order' => 2], ['user_id' => $mitra->id, 'title' => 'Cemilan Enak & Renyah', 'image' => 'seeders/banner_kuliner2.jpg', 'is_active' => true]);

            $products = [
                // Makanan
                ['name' => 'Nasi Goreng Spesial', 'price' => 25000, 'category_slug' => 'makanan', 'is_best_seller' => true],
                ['name' => 'Ayam Bakar Madu', 'price' => 35000, 'category_slug' => 'makanan', 'is_new_arrival' => true],
                ['name' => 'Soto Ayam Lamongan', 'price' => 20000, 'category_slug' => 'makanan'],
                // Minuman
                ['name' => 'Es Teh Manis Jumbo', 'price' => 8000, 'category_slug' => 'minuman'],
                ['name' => 'Jus Alpukat Segar', 'price' => 15000, 'category_slug' => 'minuman', 'is_best_seller' => true],
                // Snack
                ['name' => 'Keripik Singkong Balado', 'price' => 12000, 'category_slug' => 'snack', 'is_hot_sale' => true],
                ['name' => 'Tahu Crispy', 'price' => 10000, 'category_slug' => 'snack'],
                // Kue & Roti
                ['name' => 'Bolu Pandan Klasik', 'price' => 30000, 'category_slug' => 'kue-roti', 'is_new_arrival' => true],
            ];

            foreach ($products as $productData) {
                // Mencari ID sub-kategori dari koleksi yang sudah diambil
                $subCategoryId = $subCategories[$productData['category_slug']]->id;

                $product = Product::updateOrCreate(
                    ['name' => $productData['name'], 'user_id' => $mitra->id],
                    [
                        'slug' => Str::slug($productData['name']) . '-' . uniqid(),
                        'price' => $productData['price'],
                        'sub_category_id' => $subCategoryId,
                        'main_image' => 'seeders/product_kuliner.jpg',
                        'is_best_seller' => $productData['is_best_seller'] ?? false,
                        'is_new_arrival' => $productData['is_new_arrival'] ?? false,
                        'is_hot_sale' => $productData['is_hot_sale'] ?? false,
                    ]
                );
                $product->variants()->create(['size' => 'Original', 'stock' => 50]);
            }
            
            $this->command->info("Data untuk '{$dbName}' berhasil diisi.");

        } finally {
            // KEMBALI KE KONEKSI DATABASE PUSAT
            config(['database.connections.mysql.database' => $originalDbName]);
            DB::purge('mysql');
            DB::reconnect('mysql');
            $this->command->info("Kembali ke database pusat '{$originalDbName}'.");
        }

        $this->command->info('Seeder untuk Mitra Kuliner Demo berhasil dijalankan!');
    }
}