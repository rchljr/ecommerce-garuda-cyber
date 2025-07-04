<?php

namespace Database\Seeders;

use App\Models\Shop;
use App\Models\User;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Category;
use App\Models\Subdomain;
use App\Models\UserPackage;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\SubscriptionPackage;
use Illuminate\Support\Facades\Hash;

class InitialDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Nonaktifkan foreign key checks untuk truncate
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        Category::truncate();
        DB::table('sub_categories')->truncate();
        // Hapus juga data role dan permission lama agar tidak duplikat
        DB::table('roles')->truncate();
        DB::table('permissions')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('model_has_permissions')->truncate();
        DB::table('role_has_permissions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Panggil seeder role dan paket terlebih dahulu untuk memastikan ketersediaan
        $this->call(RolesAndPermissionsSeeder::class);
        $businessPackage = SubscriptionPackage::firstOrCreate(
            ['package_name' => 'Business Plan'],
            [
                'description' => 'Untuk bisnis menengah dan besar dengan kebutuhan lanjutan',
                'monthly_price' => 300000,
                'yearly_price' => 3000000,
                'yearly_discount' => 17,
                'is_trial' => false,
                'trial_days' => 0,
            ]
        );

        // 1. Buat User
        $admin = User::create([
            'id' => (string) Str::uuid(),
            'name' => 'Admin GCI',
            'email' => 'rachellfazaa@gmail.com',
            'password' => Hash::make('admin123'),
            'status' => 'active',
        ]);

        $mitra = User::firstOrCreate(
            ['email' => 'mitra@gmail.com'],
            [
                'name' => 'Jhon Doe',
                'password' => Hash::make('mitra123'),
                'phone' => '6281234567891',
                'position' => 'Pemilik Usaha',
                'status' => 'active',
            ]
        );

        $customer = User::firstOrCreate(
            ['email' => 'customer@gmail.com'],
            [
                'name' => 'Pelanggan Setia',
                'password' => Hash::make('customer123'),
                'phone' => '6281211112222',
                'status' => 'active',
            ]
        );

        // 2. Tetapkan role
        $admin->assignRole('admin');
        $mitra->assignRole('mitra');
        $customer->assignRole('customer');

        // 3. Buat Toko (Shop)
        Shop::firstOrCreate(
            ['user_id' => $mitra->id],
            [
                'shop_name' => 'Toko Sejahtera Makmur',
                'year_founded' => '2024-01-01',
                'shop_address' => 'Jl. Jenderal Sudirman No. 45, Pekanbaru',
                'product_categories' => 'pakaian-dan-aksesoris', // Slug kategori
                'shop_photo' => '"D:\Collage\Semester 8 - Proyek Akhir\ecommerce\ecommerce-garuda\public\storage\ktp\2ab455ab-f83b-4318-b5cd-b564cd3563ba.png"', // Path contoh
                'ktp' => '"D:\Collage\Semester 8 - Proyek Akhir\ecommerce\ecommerce-garuda\public\storage\ktp\2ab455ab-f83b-4318-b5cd-b564cd3563ba.png"', // Path contoh
            ]
        );

        // 4. Buat Subdomain
        Subdomain::firstOrCreate(
            ['user_id' => $mitra->id],
            [
                'subdomain_name' => 'sejahtera-makmur',
                'status' => 'active', 
            ]
        );

        // 5. Buat Paket Pengguna (UserPackage)
        UserPackage::firstOrCreate(
            ['user_id' => $mitra->id],
            [
                'subs_package_id' => $businessPackage->id,
                'plan_type' => 'yearly',
                'price_paid' => $businessPackage->yearly_price,
                'active_date' => now(),
                'expired_date' => now()->addYear(),
                'status' => 'active', 
            ]
        );

        // 6. Buat Order yang sudah selesai
        $order = Order::firstOrCreate(
            ['user_id' => $mitra->id, 'status' => 'completed'],
            [
                'order_date' => now()->subDay(),
                'total_price' => $businessPackage->yearly_price,
            ]
        );

        // 7. Buat Pembayaran (Payment) yang sudah lunas
        Payment::firstOrCreate(
            ['order_id' => $order->id],
            [
                'user_id' => $mitra->id,
                'subs_package_id' => $businessPackage->id,
                'midtrans_order_id' => 'SAMPLE-ACTIVE-' . $order->id,
                'midtrans_transaction_status' => 'settlement', // Status lunas
                'midtrans_payment_type' => 'bank_transfer',
                'total_payment' => $businessPackage->yearly_price,
                'midtrans_response' => json_encode(['message' => 'Sample successful payment for active mitra.']),
            ]
        );

        // 8. Data Kategori dan Sub-kategori
        $categoriesData = [
            'Kuliner' => ['Makanan', 'Minuman', 'Snack', 'Kue & Roti','Lainnya'],
            'Pakaian & Aksesoris' => ['Baju Wanita', 'Baju Pria', 'Sepatu', 'Tas', 'Jam Tangan', 'Perhiasan', 'Aksesoris Lainnya'],
            'Elektronik' => ['Rumah Tangga', 'Hiburan & Audio', 'Komputer & Aksesoris', 'Elektronik Lainnya'],
        ];

        // 9. Looping untuk membuat setiap kategori dan sub-kategorinya
        foreach ($categoriesData as $categoryName => $subCategories) {
            $category = Category::create([
                'id' => (string) Str::uuid(),
                'name' => $categoryName,
                'slug' => Str::slug($categoryName),
            ]);

            foreach ($subCategories as $subCategoryName) {
                $category->subcategories()->create([
                    'id' => (string) Str::uuid(),
                    'name' => $subCategoryName,
                    'slug' => Str::slug($subCategoryName),
                ]);
            }
        }
    }
}
