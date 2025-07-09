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

class MitraTokoSeeder extends Seeder
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
        $template = Template::where('path', 'template1')->first();
        $subCategory = SubCategory::where('slug', 'baju-wanita')->first();

        if (!$businessPackage || !$template || !$subCategory) {
            $this->command->error('Pastikan SubscriptionPackage, Template, dan SubCategory sudah ada di database sebelum menjalankan seeder ini.');
            return;
        }

        // 1. Buat User Mitra
        $mitra = User::firstOrCreate(
            ['email' => 'mitra@gmail.com'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('mitra123'),
                'phone' => '6281298765432',
                'position' => 'Pemilik Usaha',
                'status' => 'active',
            ]
        );
        $mitra->assignRole('mitra');

        // 2. Buat Toko (Shop)
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

        // 3. Buat Subdomain
        $subdomain = Subdomain::updateOrCreate(
            ['user_id' => $mitra->id],
            ['subdomain_name' => 'toko-maju-jaya', 'status' => 'active']
        );

        // 4. Buat Tenant
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

        // 6. Buat Data Kontak Toko
        Contact::updateOrCreate(
            ['user_id' => $mitra->id], 
            [
                'address_line1' => 'Jl. Pahlawan No. 123',
                'city' => 'Jakarta',
                'phone' => '6281298765432',
                'email' => 'support@tokomajujaya.com',
                'working_hours' => 'Senin - Sabtu: 09:00 - 21:00',
            ]
        );

        // 7. Buat Hero Sliders
        Hero::updateOrCreate(['user_id' => $mitra->id, 'order' => 1], ['title' => 'Koleksi Musim Panas', 'subtitle' => 'Diskon Hingga 30%', 'image' => 'seeders/hero1.jpg', 'is_active' => true]);
        Hero::updateOrCreate(['user_id' => $mitra->id, 'order' => 2], ['title' => 'Gaya Kasual Terbaik', 'subtitle' => 'Tampil Beda Setiap Hari', 'image' => 'seeders/hero2.jpg', 'is_active' => true]);

        // 8. Buat Banner Promosi
        Banner::updateOrCreate(['user_id' => $mitra->id, 'order' => 1], ['title' => 'Aksesoris Wajib Punya', 'image' => 'seeders/banner1.jpg', 'is_active' => true]);
        Banner::updateOrCreate(['user_id' => $mitra->id, 'order' => 2], ['title' => 'Tas & Dompet Terbaru', 'image' => 'seeders/banner2.jpg', 'is_active' => true]);

        // 9. Buat Beberapa Produk
        $products = [
            ['name' => 'Blouse Wanita Elegan', 'price' => 185000, 'is_best_seller' => true],
            ['name' => 'Kemeja Pria Lengan Panjang', 'price' => 220000, 'is_new_arrival' => true],
            ['name' => 'Gaun Pesta Malam', 'price' => 350000, 'is_hot_sale' => true],
        ];

        foreach ($products as $productData) {
            $product = Product::updateOrCreate(
                ['user_id' => $mitra->id, 'name' => $productData['name']],
                [
                    'slug' => Str::slug($productData['name']) . '-' . uniqid(),
                    'short_description' => 'Deskripsi singkat untuk ' . $productData['name'],
                    'price' => $productData['price'],
                    'sub_category_id' => $subCategory->id,
                    'main_image' => 'seeders/product.jpg',
                    'is_best_seller' => $productData['is_best_seller'] ?? false,
                    'is_new_arrival' => $productData['is_new_arrival'] ?? false,
                    'is_hot_sale' => $productData['is_hot_sale'] ?? false,
                ]
            );
            // Buat varian untuk setiap produk
            $product->variants()->delete(); // Hapus varian lama jika ada
            $product->variants()->create(['color' => 'Merah', 'size' => 'M', 'stock' => 10]);
            $product->variants()->create(['color' => 'Biru', 'size' => 'L', 'stock' => 15]);
        }

        $this->command->info('Seeder untuk Toko Mitra Demo berhasil dijalankan!');
    }
}