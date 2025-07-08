<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Shop;
use App\Models\Subdomain;
use App\Models\Tenant;
use App\Models\UserPackage;
use App\Models\Order;
use App\Models\Payment;
use App\Models\SubscriptionPackage;
use App\Models\Template;
use App\Models\Contact;
use App\Models\Hero;
use App\Models\Banner;
use App\Models\Product;
use App\Models\SubCategory;
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
        // Pastikan ada paket dan template untuk digunakan
        $businessPackage = SubscriptionPackage::where('package_name', 'Business Plan')->first();
        
        // PERUBAHAN 1: Menggunakan template2
        $template = Template::where('path', 'template2')->first(); 
        
        // PERUBAHAN 2: Mengambil kategori makanan/minuman
        // Pastikan Anda memiliki slug 'makanan-ringan' di tabel sub_categories
        $subCategory = SubCategory::where('slug', 'Snack')->first();

        if (!$businessPackage || !$template || !$subCategory) {
            $this->command->error('Pastikan SubscriptionPackage "Business Plan", Template "template2", dan SubCategory "makanan-ringan" sudah ada di database.');
            return;
        }

        // 1. Buat User Mitra Kuliner
        $mitra = User::firstOrCreate(
            ['email' => 'mitra.kuliner@gmail.com'],
            [
                'name' => 'Siti Aminah',
                'password' => Hash::make('kuliner123'),
                'phone' => '6285712345678',
                'position' => 'Pemilik Resto',
                'status' => 'active',
            ]
        );
        $mitra->assignRole('mitra');

        // 2. Buat Toko (Shop) Kuliner
        $shop = Shop::updateOrCreate(
            ['user_id' => $mitra->id],
            [
                'shop_name' => 'Dapur Lezat Bunda',
                'year_founded' => '2022-01-01',
                'shop_address' => 'Jl. Kuliner No. 45, Bandung',
                'product_categories' => 'kuliner',
                'shop_photo' => 'seeders/shop_kuliner_photo.jpg',
                'ktp' => 'seeders/ktp_kuliner.jpg',
            ]
        );

        // 3. Buat Subdomain Kuliner
        $subdomain = Subdomain::updateOrCreate(
            ['user_id' => $mitra->id],
            ['subdomain_name' => 'dapur-lezat-bunda', 'status' => 'active']
        );

        // 4. Buat Tenant Kuliner
        Tenant::updateOrCreate(
            ['user_id' => $mitra->id],
            ['subdomain_id' => $subdomain->id, 'template_id' => $template->id]
        );

        // 5. Buat Paket Langganan Aktif
        UserPackage::updateOrCreate(
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

        // 6. Buat Data Kontak Toko Kuliner
        Contact::updateOrCreate(
            ['id' => 2], // Gunakan ID yang berbeda dari seeder pertama
            [
                'address_line1' => 'Jl. Kuliner No. 45',
                'city' => 'Bandung',
                'phone' => '6285712345678',
                'email' => 'pesan@dapurlezatbunda.com',
                'working_hours' => 'Selasa - Minggu: 10:00 - 22:00',
            ]
        );

        // 7. Buat Hero Sliders Kuliner
        Hero::updateOrCreate(['user_id' => $mitra->id, 'order' => 1], ['title' => 'Aneka Sambal Nusantara', 'subtitle' => 'Pedasnya Bikin Nagih!', 'image' => 'seeders/kuliner_hero1.jpg', 'is_active' => true]);
        Hero::updateOrCreate(['user_id' => $mitra->id, 'order' => 2], ['title' => 'Cemilan Sehat & Nikmat', 'subtitle' => 'Teman Santai Setiap Saat', 'image' => 'seeders/kuliner_hero2.jpg', 'is_active' => true]);

        // 8. Buat Banner Promosi Kuliner
        Banner::updateOrCreate(['user_id' => $mitra->id, 'order' => 1], ['title' => 'Paket Hemat Keluarga', 'image' => 'seeders/kuliner_banner1.jpg', 'is_active' => true]);
        Banner::updateOrCreate(['user_id' => $mitra->id, 'order' => 2], ['title' => 'Minuman Segar Alami', 'image' => 'seeders/kuliner_banner2.jpg', 'is_active' => true]);

        // 9. Buat Beberapa Produk Kuliner
        $products = [
            ['name' => 'Keripik Singkong Balado', 'price' => 25000, 'is_best_seller' => true],
            ['name' => 'Rendang Daging Kemasan', 'price' => 75000, 'is_new_arrival' => true],
            ['name' => 'Es Kopi Susu Gula Aren', 'price' => 18000, 'is_hot_sale' => true],
        ];

        foreach ($products as $productData) {
            $product = Product::updateOrCreate(
                ['user_id' => $mitra->id, 'name' => $productData['name']],
                [
                    'slug' => Str::slug($productData['name']) . '-' . uniqid(),
                    'short_description' => 'Deskripsi lezat untuk ' . $productData['name'],
                    'price' => $productData['price'],
                    'sub_category_id' => $subCategory->id,
                    'main_image' => 'seeders/product_kuliner.jpg',
                    'is_best_seller' => $productData['is_best_seller'] ?? false,
                    'is_new_arrival' => $productData['is_new_arrival'] ?? false,
                    'is_hot_sale' => $productData['is_hot_sale'] ?? false,
                ]
            );
            // Buat varian yang relevan untuk makanan
            $product->variants()->delete();
            $product->variants()->create(['size' => '250g', 'stock' => 20]);
            $product->variants()->create(['size' => '500g', 'stock' => 12]);
        }

        $this->command->info('Seeder untuk Mitra Kuliner berhasil dijalankan!');
    }
}