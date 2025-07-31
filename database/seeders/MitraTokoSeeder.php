<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\Contact;
use App\Models\Hero;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Shop;
use App\Models\SubCategory;
use App\Models\Subdomain;
use App\Models\SubscriptionPackage;
use App\Models\Template;
use App\Models\Tenant;
use App\Models\User;
use App\Models\UserPackage;
use App\Models\Varian; // Menggunakan model Varian yang benar
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MitraTokoSeeder extends Seeder
{
    /**
     * Helper untuk membuat path gambar palsu.
     */
    private function getImagePath($folder, $originalFileName)
    {
        $extension = pathinfo($originalFileName, PATHINFO_EXTENSION);
        return "{$folder}/" . Str::uuid() . ".{$extension}";
    }

    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::transaction(function () {
            // 1. Ambil data master yang diperlukan
            $template1 = Template::where('path', 'template1')->firstOrFail();
            $template2 = Template::where('path', 'template2')->firstOrFail();

            $subCatBajuPria = SubCategory::where('slug', 'baju-pria')->firstOrFail();
            $subCatBajuWanita = SubCategory::where('slug', 'baju-wanita')->firstOrFail();
            $subCatKueRoti = SubCategory::where('slug', 'kue-roti')->firstOrFail();

            // Ambil paket "Business Plan" yang sudah dibuat oleh SubscriptionPackageSeeder
            $businessPackage = SubscriptionPackage::where('package_name', 'Business Plan')->firstOrFail();

            // 2. Buat setiap toko
            $this->createFashionStore1($template1, $businessPackage, $subCatBajuPria, $subCatBajuWanita);
            $this->createFashionStore2($template1, $businessPackage, $subCatBajuWanita);
            $this->createFoodStore($template2, $businessPackage, $subCatKueRoti);
        });
    }

    /**
     * Membuat Toko Fashion Pertama: "Batik Nusantara"
     */
    private function createFashionStore1(Template $template, SubscriptionPackage $package, SubCategory $subCatPria, SubCategory $subCatWanita)
    {
        // 1. Buat User (Mitra)
        $user = User::create([
            'name' => 'Wahyudi Pratama',
            'email' => 'wahyudirayhan11@gmail.com',
            'password' => Hash::make('mitra123'),
            'phone' => '081234567890',
            'status' => 'active',
            'alamat' => 'Jl. Batik No. 1, Yogyakarta',
        ]);
        $user->assignRole('mitra');

        // 2. Buat Toko
        $shop = Shop::create([
            'user_id' => $user->id,
            'shop_name' => 'Batik Nusantara',
            'year_founded' => '2020-01-15',
            'shop_address' => 'Jl. Malioboro No. 123, Yogyakarta',
            'postal_code' => '55213',
            'product_categories' => 'pakaian-aksesoris',
            'shop_photo' => $this->getImagePath('shop_photos', 'shop_batik.jpg'),
            'ktp' => $this->getImagePath('ktp', 'shop_batik.jpg'),
        ]);

        // 3. Buat Subdomain
        $subdomain = Subdomain::create([
            'user_id' => $user->id,
            'shop_id' => $shop->id,
            'subdomain_name' => 'batiknusantara',
            'status' => 'active',
            'publication_status' => 'published',
        ]);

        // 4. Buat Order & Pembayaran untuk paket langganan
        $order = Order::create([
            'order_number' => 'SUB-' . strtoupper(Str::random(8)),
            'user_id' => $user->id,
            'shop_id' => $shop->id,
            'subdomain_id' => $subdomain->id,
            'status' => 'completed',
            'total_price' => $package->yearly_price,
            'subtotal' => $package->yearly_price,
            'order_date' => Carbon::now(),
        ]);
        Payment::create([
            'user_id' => $user->id,
            'order_id' => $order->id,
            'subs_package_id' => $package->id,
            'midtrans_order_id' => $order->id,
            'midtrans_transaction_status' => 'settlement',
            'midtrans_payment_type' => 'bank_transfer',
            'midtrans_response' => 'success',
            'total_payment' => $package->yearly_price,
        ]);

        // 5. Aktifkan Paket Langganan untuk User
        UserPackage::create([
            'user_id' => $user->id,
            'subs_package_id' => $package->id,
            'plan_type' => 'yearly',
            'price_paid' => $package->yearly_price,
            'active_date' => Carbon::now(),
            'expired_date' => Carbon::now()->addYear(),
            'status' => 'active',
        ]);

        // 6. Buat Tenant
        Tenant::create(['user_id' => $user->id, 'template_id' => $template->id, 'subdomain_id' => $subdomain->id]);

        // 7. Buat Kontak
        Contact::create(['user_id' => $user->id, 'phone' => $user->phone, 'email' => 'kontak@batiknusantara.com', 'address_line1' => $shop->shop_address, 'city' => 'Yogyakarta']);

        // 8. Buat Hero, Banner, Produk & Varian
        Hero::create(['shop_id' => $shop->id, 'title' => 'Koleksi Batik Premium', 'subtitle' => 'Edisi Terbatas', 'image' => $this->getImagePath('heroes', 'hero_batik_1.jpg'), 'button_text' => 'Lihat Koleksi']);
        Banner::create(['shop_id' => $shop->id, 'user_id' => $user->id, 'title' => 'Kemeja Pria', 'image' => $this->getImagePath('banners', 'banner_kemeja.jpg'), 'button_text' => 'Beli']);

        $product1 = Product::create(['user_id' => $user->id, 'shop_id' => $shop->id, 'sub_category_id' => $subCatPria->id, 'name' => 'Kemeja Batik Parang', 'slug' => 'kemeja-batik-parang', 'short_description' => 'Kemeja batik katun premium.', 'description' => 'Kemeja batik katun primisima dengan motif parang klasik, cocok untuk acara formal maupun kasual.', 'main_image' => $this->getImagePath('products', 'kemeja_parang.jpg'), 'gallery_image_paths' => json_encode([$this->getImagePath('gallery', 'gallery1.jpg'), $this->getImagePath('gallery', 'gallery2.jpg')]), 'status' => 'active', 'is_best_seller' => true]);
        Varian::create(['product_id' => $product1->id, 'name' => 'M / Cokelat', 'price' => 250000, 'modal_price' => 180000, 'profit_percentage' => 38.89, 'stock' => 10, 'status' => 'active', 'options_data' => json_encode([['name' => 'Size', 'value' => 'M'], ['name' => 'Color', 'value' => 'Cokelat']])]);
        Varian::create(['product_id' => $product1->id, 'name' => 'L / Cokelat', 'price' => 250000, 'modal_price' => 180000, 'profit_percentage' => 38.89, 'stock' => 8, 'status' => 'active', 'options_data' => json_encode([['name' => 'Size', 'value' => 'L'], ['name' => 'Color', 'value' => 'Cokelat']])]);
    }

    /**
     * Membuat Toko Fashion Kedua: "Tenun Etnik"
     */
    private function createFashionStore2(Template $template, SubscriptionPackage $package, SubCategory $subCategory)
    {
        $user = User::create(['name' => 'Citra Lestari', 'email' => 'mitra2@gmail.com', 'password' => Hash::make('mitra123'), 'phone' => '089876543210', 'status' => 'active', 'alamat' => 'Jl. Tenun No. 2, Lombok']);
        $user->assignRole('mitra');

        $shop = Shop::create(['user_id' => $user->id, 'shop_name' => 'Tenun Etnik', 'year_founded' => '2022-03-01', 'shop_address' => 'Desa Sade, Lombok', 'postal_code' => '83573', 'product_categories' => 'pakaian-aksesoris', 'shop_photo' => $this->getImagePath('shop_photos', 'shop_tenun.jpg'), 'ktp' => $this->getImagePath('ktp', 'shop_tenun.jpg')]);

        $subdomain = Subdomain::create(['user_id' => $user->id, 'shop_id' => $shop->id, 'subdomain_name' => 'tenun-etnik', 'status' => 'active', 'publication_status' => 'published']);

        $order = Order::create(['order_number' => 'SUB-' . strtoupper(Str::random(8)), 'user_id' => $user->id, 'shop_id' => $shop->id, 'subdomain_id' => $subdomain->id, 'status' => 'completed', 'total_price' => $package->yearly_price, 'subtotal' => $package->yearly_price, 'order_date' => Carbon::now()]);
        Payment::create([
            'user_id' => $user->id,
            'order_id' => $order->id,
            'subs_package_id' => $package->id,
            'midtrans_order_id' => $order->id,
            'midtrans_transaction_status' => 'settlement',
            'midtrans_payment_type' => 'bank_transfer',
            'midtrans_response' => 'success',
            'total_payment' => $package->yearly_price,
        ]);

        UserPackage::create(['user_id' => $user->id, 'subs_package_id' => $package->id, 'plan_type' => 'yearly', 'price_paid' => $package->yearly_price, 'active_date' => Carbon::now(), 'expired_date' => Carbon::now()->addYear(), 'status' => 'active']);

        Tenant::create(['user_id' => $user->id, 'template_id' => $template->id, 'subdomain_id' => $subdomain->id]);

        Contact::create(['user_id' => $user->id, 'phone' => $user->phone, 'email' => 'info@tenun-etnik.com', 'address_line1' => $shop->shop_address, 'city' => 'Lombok Tengah']);

        Hero::create(['shop_id' => $shop->id, 'title' => 'Keindahan Tenun Ikat', 'subtitle' => 'Warisan Budaya', 'image' => $this->getImagePath('heroes', 'hero_tenun_1.jpg'), 'button_text' => 'Jelajahi']);

        $product1 = Product::create(['user_id' => $user->id, 'shop_id' => $shop->id, 'sub_category_id' => $subCategory->id, 'name' => 'Outer Tenun Sumba', 'slug' => 'outer-tenun-sumba', 'short_description' => 'Outer unisex dari kain tenun Sumba.', 'description' => 'Outer unisex dari kain tenun Sumba asli, dibuat oleh pengrajin lokal dengan pewarnaan alami.', 'main_image' => $this->getImagePath('products', 'outer_sumba.jpg'), 'status' => 'active', 'is_new_arrival' => true]);
        Varian::create(['product_id' => $product1->id, 'name' => 'All Size', 'price' => 450000, 'modal_price' => 320000, 'profit_percentage' => 40.63, 'stock' => 7, 'status' => 'active', 'options_data' => json_encode([['name' => 'Size', 'value' => 'All Size']])]);
    }

    /**
     * Membuat Toko Makanan: "Roti Enak Bakery"
     */
    private function createFoodStore(Template $template, SubscriptionPackage $package, SubCategory $subCategory)
    {
        $user = User::create(['name' => 'Budi Santoso', 'email' => 'mitra3@gmail.com', 'password' => Hash::make('mitra123'), 'phone' => '081122334455', 'status' => 'active', 'alamat' => 'Jl. Roti No. 3, Bandung']);
        $user->assignRole('mitra');

        $shop = Shop::create(['user_id' => $user->id, 'shop_name' => 'Roti Enak Bakery', 'year_founded' => '2019-08-17', 'shop_address' => 'Jl. Braga No. 55, Bandung', 'postal_code' => '40111', 'product_categories' => 'kuliner', 'shop_photo' => $this->getImagePath('shop_photos', 'shop_bakery.jpg'), 'ktp' => $this->getImagePath('ktp', 'shop_bakery.jpg')]);

        $subdomain = Subdomain::create(['user_id' => $user->id, 'shop_id' => $shop->id, 'subdomain_name' => 'rotienak', 'status' => 'active', 'publication_status' => 'published']);

        $order = Order::create(['order_number' => 'SUB-' . strtoupper(Str::random(8)), 'user_id' => $user->id, 'shop_id' => $shop->id, 'subdomain_id' => $subdomain->id, 'status' => 'completed', 'total_price' => $package->yearly_price, 'subtotal' => $package->yearly_price, 'order_date' => Carbon::now()]);
        Payment::create([
            'user_id' => $user->id,
            'order_id' => $order->id,
            'subs_package_id' => $package->id,
            'midtrans_order_id' => $order->id,
            'midtrans_transaction_status' => 'settlement',
            'midtrans_payment_type' => 'bank_transfer',
            'midtrans_response' => 'success',
            'total_payment' => $package->yearly_price,
        ]);
        
        UserPackage::create(['user_id' => $user->id, 'subs_package_id' => $package->id, 'plan_type' => 'yearly', 'price_paid' => $package->yearly_price, 'active_date' => Carbon::now(), 'expired_date' => Carbon::now()->addYear(), 'status' => 'active']);

        Tenant::create(['user_id' => $user->id, 'template_id' => $template->id, 'subdomain_id' => $subdomain->id]);

        Contact::create(['user_id' => $user->id, 'phone' => $user->phone, 'email' => 'order@rotienak.com', 'address_line1' => $shop->shop_address, 'city' => 'Bandung']);

        Hero::create(['shop_id' => $shop->id, 'title' => 'Roti Fresh From The Oven', 'subtitle' => 'Promo Beli 2 Gratis 1', 'image' => $this->getImagePath('heroes', 'hero_bakery_1.jpg'), 'button_text' => 'Pesan Sekarang']);

        $product1 = Product::create(['user_id' => $user->id, 'shop_id' => $shop->id, 'sub_category_id' => $subCategory->id, 'name' => 'Croissant Butter', 'slug' => 'croissant-butter', 'short_description' => 'Croissant renyah dan wangi.', 'description' => 'Croissant renyah dengan aroma mentega premium, cocok untuk sarapan atau teman minum kopi.', 'main_image' => $this->getImagePath('products', 'croissant.jpg'), 'status' => 'active', 'is_hot_sale' => true]);
        Varian::create(['product_id' => $product1->id, 'name' => 'Original', 'price' => 15000, 'modal_price' => 10000, 'profit_percentage' => 50.00, 'stock' => 50, 'status' => 'active', 'options_data' => json_encode([['name' => 'Topping', 'value' => 'Original']])]);
        Varian::create(['product_id' => $product1->id, 'name' => 'Keju', 'price' => 18000, 'modal_price' => 12000, 'profit_percentage' => 50.00, 'stock' => 30, 'status' => 'active', 'options_data' => json_encode([['name' => 'Topping', 'value' => 'Keju']])]);
    }
}