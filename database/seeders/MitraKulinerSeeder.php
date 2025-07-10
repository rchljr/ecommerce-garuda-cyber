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
use Illuminate\Support\Facades\DB; // WAJIB DI-IMPORT
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
        $this->command->info('Memulai Seeder untuk Mitra Toko Demo...');

        // Data ini diambil dari database pusat
        $businessPackage = SubscriptionPackage::where('package_name', 'Business Plan')->first();
        $template = Template::where('path', 'template1')->first();
        $subCategory = SubCategory::where('slug', 'baju-wanita')->first();

        if (!$businessPackage || !$template || !$subCategory) {
            $this->command->error('Pastikan SubscriptionPackage, Template, dan SubCategory sudah ada sebelum menjalankan seeder ini.');
            return;
        }

        // Membuat data di database pusat
        $mitra = User::firstOrCreate(['email' => 'mitra@gmail.com'], ['name' => 'Budi Santoso', 'password' => Hash::make('mitra123'), 'status' => 'active']);
        $mitra->assignRole('mitra');

        $shop = Shop::updateOrCreate(
            ['user_id' => $mitra->id],
            [
                'shop_name' => 'Toko Maju Jaya',
                'year_founded' => '2023-01-01',
                'shop_address' => 'Jl. Pahlawan No. 123, Jakarta',
                'product_categories' => 'pakaian-aksesoris',
                'shop_photo' => 'seeders/shop_photo.jpg',
                'ktp' => 'seeders/ktp.jpg',
            ]
        );
        $subdomain = Subdomain::updateOrCreate(['user_id' => $mitra->id], ['subdomain_name' => 'toko-maju-jaya', 'status' => 'active']);
        
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

            Contact::updateOrCreate(['id' => 1], ['address_line1' => 'Jl. Pahlawan No. 123', 'city' => 'Jakarta', 'email' => 'support@tokomajujaya.com']);
            
            // PERBAIKAN: Menambahkan 'user_id'
            Hero::updateOrCreate(['order' => 1], ['user_id' => $mitra->id, 'title' => 'Koleksi Musim Panas', 'subtitle' => 'Diskon Hingga 30%', 'image' => 'seeders/hero1.jpg', 'is_active' => true]);
            Hero::updateOrCreate(['order' => 2], ['user_id' => $mitra->id, 'title' => 'Gaya Kasual Terbaik', 'subtitle' => 'Tampil Beda Setiap Hari', 'image' => 'seeders/hero2.jpg', 'is_active' => true]);

            // PERBAIKAN: Menambahkan 'user_id'
            Banner::updateOrCreate(['order' => 1], ['user_id' => $mitra->id, 'title' => 'Aksesoris Wajib Punya', 'image' => 'seeders/banner1.jpg', 'is_active' => true]);
            Banner::updateOrCreate(['order' => 2], ['user_id' => $mitra->id, 'title' => 'Tas & Dompet Terbaru', 'image' => 'seeders/banner2.jpg', 'is_active' => true]);

            $products = [
                ['name' => 'Blouse Wanita Elegan', 'price' => 185000, 'is_best_seller' => true],
                ['name' => 'Kemeja Pria Lengan Panjang', 'price' => 220000, 'is_new_arrival' => true],
                ['name' => 'Gaun Pesta Malam', 'price' => 350000, 'is_hot_sale' => true],
            ];

            foreach ($products as $productData) {
                // PERBAIKAN: Menambahkan 'user_id'
                $product = Product::updateOrCreate(
                    ['name' => $productData['name']],
                    [
                        'user_id' => $mitra->id,
                        'slug' => Str::slug($productData['name']) . '-' . uniqid(),
                        'price' => $productData['price'],
                        'sub_category_id' => $subCategory->id,
                        'main_image' => 'seeders/product.jpg',
                    ]
                );
                $product->variants()->create(['color' => 'Merah', 'size' => 'M', 'stock' => 10]);
                $product->variants()->create(['color' => 'Biru', 'size' => 'L', 'stock' => 15]);
            }
            
            $this->command->info("Data untuk '{$dbName}' berhasil diisi.");

        } finally {
            // KEMBALI KE KONEKSI DATABASE PUSAT
            config(['database.connections.mysql.database' => $originalDbName]);
            DB::purge('mysql');
            DB::reconnect('mysql');
            $this->command->info("Kembali ke database pusat '{$originalDbName}'.");
        }

        $this->command->info('Seeder untuk Toko Mitra Demo berhasil dijalankan!');
    }
}
