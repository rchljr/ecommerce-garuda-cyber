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
use App\Models\Varian;
use App\Models\Voucher;
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
            // 1. Ambil data master
            $template1 = Template::where('path', 'template1')->firstOrFail();
            $template2 = Template::where('path', 'template2')->firstOrFail();

            $subCatBajuPria = SubCategory::where('slug', 'baju-pria')->firstOrFail();
            $subCatBajuWanita = SubCategory::where('slug', 'baju-wanita')->firstOrFail();
            $subCatKueRoti = SubCategory::where('slug', 'kue-roti')->firstOrFail();
            $subCatTas = SubCategory::where('slug', 'tas')->firstOrFail();
            $subCatSnack = SubCategory::where('slug', 'snack')->firstOrFail();

            $businessPackage = SubscriptionPackage::where('package_name', 'Business Plan')->firstOrFail();

            // 2. Buat setiap toko
            $this->createFashionStore1($template1, $businessPackage, $subCatBajuPria, $subCatBajuWanita);
            $this->createFashionStore2($template1, $businessPackage, $subCatBajuWanita, $subCatTas);
            $this->createFoodStore($template2, $businessPackage, $subCatKueRoti, $subCatSnack);
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
            'ktp' => $this->getImagePath('documents', 'ktp_dummy.pdf'),
            'sku' => $this->getImagePath('documents', 'sku_dummy.pdf'),
            'npwp' => $this->getImagePath('documents', 'npwp_dummy.pdf'),
            'nib' => $this->getImagePath('documents', 'nib_dummy.pdf'),
            'iumk' => $this->getImagePath('documents', 'iumk_dummy.pdf'),
        ]);

        // 3. Buat Subdomain
        $subdomain = Subdomain::create(['user_id' => $user->id, 'shop_id' => $shop->id, 'subdomain_name' => 'batiknusantara', 'status' => 'active', 'publication_status' => 'published']);

        // 4. Buat Order & Pembayaran untuk paket langganan
        $orderGroupId = Str::uuid();
        $order = Order::create(['order_number' => 'SUB-' . strtoupper(Str::random(8)), 'user_id' => $user->id, 'shop_id' => $shop->id, 'order_group_id' => $orderGroupId, 'subdomain_id' => $subdomain->id, 'status' => 'completed', 'total_price' => $package->yearly_price, 'subtotal' => $package->yearly_price, 'order_date' => Carbon::now()]);

        Payment::create([
            'user_id' => $user->id,
            'order_id' => $order->id,
            'order_group_id' => $orderGroupId,
            'subs_package_id' => $package->id,
            'midtrans_order_id' => $order->id,
            'midtrans_transaction_status' => 'settlement',
            'midtrans_payment_type' => 'bank_transfer',
            'total_payment' => $package->yearly_price,
            'midtrans_response' => json_encode(['transaction_id' => 'TRANS-' . Str::uuid(), 'status_code' => '200', 'status_message' => 'Success, transaction is found'])
        ]);

        // 5. Aktifkan Paket Langganan untuk User
        UserPackage::create(['user_id' => $user->id, 'subs_package_id' => $package->id, 'plan_type' => 'yearly', 'price_paid' => $package->yearly_price, 'active_date' => Carbon::now(), 'expired_date' => Carbon::now()->addYear(), 'status' => 'active']);

        // 6. Buat Tenant & Kontak
        Tenant::create(['user_id' => $user->id, 'template_id' => $template->id, 'subdomain_id' => $subdomain->id]);
        Contact::create(['user_id' => $user->id, 'phone' => $user->phone, 'email' => 'kontak@batiknusantara.com', 'address_line1' => $shop->shop_address, 'city' => 'Yogyakarta']);

        // 7. Buat Hero, Banner
        Hero::create(['shop_id' => $shop->id, 'title' => 'Koleksi Batik Premium', 'subtitle' => 'Edisi Terbatas', 'image' => $this->getImagePath('heroes', 'hero_batik_1.jpg'), 'button_text' => 'Lihat Koleksi', 'order' => 1, 'is_active' => true]);
        Banner::create(['shop_id' => $shop->id, 'user_id' => $user->id, 'title' => 'Kemeja Pria', 'image' => $this->getImagePath('banners', 'banner_kemeja.jpg'), 'button_text' => 'Beli', 'order' => 1, 'is_active' => true]);

        // 8. Buat 5 Produk & Variannya
        $productsData = [
            ['sub_category_id' => $subCatPria->id, 'name' => 'Kemeja Batik Parang', 'is_best_seller' => true, 'variants' => [['options' => [['name' => 'Size', 'value' => 'M']], 'modal' => 180000, 'profit' => 39, 'stock' => 10], ['options' => [['name' => 'Size', 'value' => 'L']], 'modal' => 180000, 'profit' => 39, 'stock' => 8]]],
            ['sub_category_id' => $subCatWanita->id, 'name' => 'Dress Batik Mega Mendung', 'is_new_arrival' => true, 'variants' => [['options' => [['name' => 'Size', 'value' => 'S']], 'modal' => 250000, 'profit' => 40, 'stock' => 15], ['options' => [['name' => 'Size', 'value' => 'M']], 'modal' => 250000, 'profit' => 40, 'stock' => 12]]],
            ['sub_category_id' => $subCatPria->id, 'name' => 'Kemeja Batik Slim Fit', 'is_hot_sale' => true, 'variants' => [['options' => [['name' => 'Size', 'value' => 'M'], ['name' => 'Color', 'value' => 'Hitam']], 'modal' => 200000, 'profit' => 35, 'stock' => 20], ['options' => [['name' => 'Size', 'value' => 'L'], ['name' => 'Color', 'value' => 'Hitam']], 'modal' => 200000, 'profit' => 35, 'stock' => 15]]],
            ['sub_category_id' => $subCatWanita->id, 'name' => 'Blouse Batik Modern', 'variants' => [['options' => [['name' => 'Size', 'value' => 'All Size']], 'modal' => 150000, 'profit' => 50, 'stock' => 25]]],
            ['sub_category_id' => $subCatPria->id, 'name' => 'Celana Batik Santai', 'variants' => [['options' => [['name' => 'Size', 'value' => 'L']], 'modal' => 120000, 'profit' => 45, 'stock' => 30], ['options' => [['name' => 'Size', 'value' => 'XL']], 'modal' => 120000, 'profit' => 45, 'stock' => 18]]],
        ];

        foreach ($productsData as $data) {
            $product = Product::create([
                'user_id' => $user->id,
                'shop_id' => $shop->id,
                'sub_category_id' => $data['sub_category_id'],
                'name' => $data['name'],
                'slug' => Str::slug($data['name']) . '-' . uniqid(),
                'short_description' => 'Deskripsi singkat untuk ' . $data['name'],
                'description' => 'Deskripsi panjang dan lengkap untuk produk ' . $data['name'] . '.',
                'main_image' => $this->getImagePath('products', 'product.jpg'),
                'gallery_image_paths' => [$this->getImagePath('gallery', 'gallery1.jpg')], // [FIX] Simpan sebagai array PHP
                'status' => 'active',
                'is_best_seller' => $data['is_best_seller'] ?? false,
                'is_new_arrival' => $data['is_new_arrival'] ?? false,
                'is_hot_sale' => $data['is_hot_sale'] ?? false,
            ]);

            foreach ($data['variants'] as $variantData) {
                $modal = $variantData['modal'];
                $profit = $variantData['profit'];
                $price = $modal * (1 + ($profit / 100));
                $name = collect($variantData['options'])->pluck('value')->implode(' / ');
                Varian::create(['product_id' => $product->id, 'name' => $name, 'price' => $price, 'modal_price' => $modal, 'profit_percentage' => $profit, 'stock' => $variantData['stock'], 'status' => 'active', 'options_data' => $variantData['options']]);
            }
        }

        // 9. Buat 3 Voucher untuk toko ini
        Voucher::create(['user_id' => $user->id, 'subdomain_id' => $subdomain->id, 'voucher_code' => 'BATIKBARU', 'description' => 'Diskon 15% untuk pelanggan baru.', 'min_spending' => 100000, 'start_date' => Carbon::now(), 'expired_date' => Carbon::now()->addMonths(3), 'discount' => 15, 'is_for_new_customer' => true, 'max_uses_per_customer' => true]);
        Voucher::create(['user_id' => $user->id, 'subdomain_id' => $subdomain->id, 'voucher_code' => 'GAJIANBATIK', 'description' => 'Diskon 20% min. belanja Rp 300.000.', 'min_spending' => 300000, 'start_date' => Carbon::now(), 'expired_date' => Carbon::now()->addMonth(), 'discount' => 20, 'is_for_new_customer' => false, 'max_uses_per_customer' => false]);
        Voucher::create(['user_id' => $user->id, 'subdomain_id' => $subdomain->id, 'voucher_code' => 'WEEKENDSERU', 'description' => 'Diskon 10% setiap akhir pekan.', 'min_spending' => 0, 'start_date' => Carbon::now()->subWeek(), 'expired_date' => Carbon::now()->addMonths(6), 'discount' => 10, 'is_for_new_customer' => false, 'max_uses_per_customer' => false]);
    }

    /**
     * Membuat Toko Fashion Kedua: "Tenun Etnik"
     */
    private function createFashionStore2(Template $template, SubscriptionPackage $package, SubCategory $subCatWanita, SubCategory $subCatTas)
    {
        $user = User::create(['name' => 'Citra Lestari', 'email' => 'mitra2@gmail.com', 'password' => Hash::make('mitra123'), 'phone' => '089876543210', 'status' => 'active', 'alamat' => 'Jl. Tenun No. 2, Lombok']);
        $user->assignRole('mitra');

        $shop = Shop::create(['user_id' => $user->id, 'shop_name' => 'Tenun Etnik', 'year_founded' => '2022-03-01', 'shop_address' => 'Desa Sade, Lombok', 'postal_code' => '83573', 'product_categories' => 'pakaian-aksesoris', 'shop_photo' => $this->getImagePath('shop_photos', 'shop_tenun.jpg'), 'ktp' => $this->getImagePath('documents', 'ktp_dummy.pdf'), 'sku' => $this->getImagePath('documents', 'sku_dummy.pdf'), 'npwp' => $this->getImagePath('documents', 'npwp_dummy.pdf'), 'nib' => $this->getImagePath('documents', 'nib_dummy.pdf'), 'iumk' => $this->getImagePath('documents', 'iumk_dummy.pdf')]);

        $subdomain = Subdomain::create(['user_id' => $user->id, 'shop_id' => $shop->id, 'subdomain_name' => 'tenun-etnik', 'status' => 'active', 'publication_status' => 'published']);

        $orderGroupId = Str::uuid();
        $order = Order::create(['order_number' => 'SUB-' . strtoupper(Str::random(8)), 'user_id' => $user->id, 'shop_id' => $shop->id, 'order_group_id' => $orderGroupId, 'subdomain_id' => $subdomain->id, 'status' => 'completed', 'total_price' => $package->yearly_price, 'subtotal' => $package->yearly_price, 'order_date' => Carbon::now()]);

        Payment::create(['user_id' => $user->id, 'order_id' => $order->id, 'order_group_id' => $orderGroupId, 'subs_package_id' => $package->id, 'midtrans_order_id' => $order->id, 'midtrans_transaction_status' => 'settlement', 'midtrans_payment_type' => 'bank_transfer', 'total_payment' => $package->yearly_price, 'midtrans_response' => json_encode(['transaction_id' => 'TRANS-' . Str::uuid(), 'status_code' => '200', 'status_message' => 'Success, transaction is found'])]);

        UserPackage::create(['user_id' => $user->id, 'subs_package_id' => $package->id, 'plan_type' => 'yearly', 'price_paid' => $package->yearly_price, 'active_date' => Carbon::now(), 'expired_date' => Carbon::now()->addYear(), 'status' => 'active']);

        Tenant::create(['user_id' => $user->id, 'template_id' => $template->id, 'subdomain_id' => $subdomain->id]);

        Contact::create(['user_id' => $user->id, 'phone' => $user->phone, 'email' => 'info@tenun-etnik.com', 'address_line1' => $shop->shop_address, 'city' => 'Lombok Tengah']);

        Hero::create(['shop_id' => $shop->id, 'title' => 'Keindahan Tenun Ikat', 'subtitle' => 'Warisan Budaya', 'image' => $this->getImagePath('heroes', 'hero_tenun_1.jpg'), 'button_text' => 'Jelajahi', 'order' => 1, 'is_active' => true]);

        $productsData = [
            ['sub_category_id' => $subCatWanita->id, 'name' => 'Outer Tenun Sumba', 'is_new_arrival' => true, 'variants' => [['options' => [['name' => 'Size', 'value' => 'All Size']], 'modal' => 320000, 'profit' => 40, 'stock' => 7]]],
            ['sub_category_id' => $subCatTas->id, 'name' => 'Tas Selempang Tenun', 'is_best_seller' => true, 'variants' => [['options' => [['name' => 'Motif', 'value' => 'Bunga']], 'modal' => 150000, 'profit' => 50, 'stock' => 15]]],
            ['sub_category_id' => $subCatWanita->id, 'name' => 'Rok Lilit Tenun', 'variants' => [['options' => [['name' => 'Warna', 'value' => 'Merah Marun']], 'modal' => 220000, 'profit' => 45, 'stock' => 10]]],
            ['sub_category_id' => $subCatTas->id, 'name' => 'Pouch Tenun Multifungsi', 'variants' => [['options' => [['name' => 'Ukuran', 'value' => 'Kecil']], 'modal' => 75000, 'profit' => 60, 'stock' => 30]]],
            ['sub_category_id' => $subCatWanita->id, 'name' => 'Syāl Tenun Hangat', 'is_hot_sale' => true, 'variants' => [['options' => [['name' => 'Pola', 'value' => 'Garis']], 'modal' => 180000, 'profit' => 40, 'stock' => 22]]],
        ];

        foreach ($productsData as $data) {
            $product = Product::create(['user_id' => $user->id, 'shop_id' => $shop->id, 'sub_category_id' => $data['sub_category_id'], 'name' => $data['name'], 'slug' => Str::slug($data['name']) . '-' . uniqid(), 'short_description' => 'Deskripsi singkat untuk ' . $data['name'], 'description' => 'Deskripsi panjang untuk ' . $data['name'], 'main_image' => $this->getImagePath('products', 'product.jpg'), 'gallery_image_paths' => [$this->getImagePath('gallery', 'gallery1.jpg')], 'status' => 'active', 'is_best_seller' => $data['is_best_seller'] ?? false, 'is_new_arrival' => $data['is_new_arrival'] ?? false, 'is_hot_sale' => $data['is_hot_sale'] ?? false]);
            foreach ($data['variants'] as $variantData) {
                $modal = $variantData['modal'];
                $profit = $variantData['profit'];
                $price = $modal * (1 + ($profit / 100));
                $name = collect($variantData['options'])->pluck('value')->implode(' / ');
                Varian::create(['product_id' => $product->id, 'name' => $name, 'price' => $price, 'modal_price' => $modal, 'profit_percentage' => $profit, 'stock' => $variantData['stock'], 'status' => 'active', 'options_data' => $variantData['options']]);
            }
        }

        Voucher::create(['user_id' => $user->id, 'subdomain_id' => $subdomain->id, 'voucher_code' => 'ETNIK10', 'description' => 'Diskon 10% untuk semua produk tenun.', 'min_spending' => 250000, 'start_date' => Carbon::now(), 'expired_date' => Carbon::now()->addMonths(2), 'discount' => 10, 'is_for_new_customer' => false, 'max_uses_per_customer' => false]);
        Voucher::create(['user_id' => $user->id, 'subdomain_id' => $subdomain->id, 'voucher_code' => 'TENUNKEREN', 'description' => 'Diskon 15% min. belanja Rp 500.000.', 'min_spending' => 500000, 'start_date' => Carbon::now(), 'expired_date' => Carbon::now()->addMonth(), 'discount' => 15, 'is_for_new_customer' => false, 'max_uses_per_customer' => true]);
        Voucher::create(['user_id' => $user->id, 'subdomain_id' => $subdomain->id, 'voucher_code' => 'ONGKIRTENUN', 'description' => 'Diskon 12% untuk ongkos kirim.', 'min_spending' => 150000, 'start_date' => Carbon::now(), 'expired_date' => Carbon::now()->addMonths(4), 'discount' => 12, 'is_for_new_customer' => false, 'max_uses_per_customer' => false]);
    }

    /**
     * Membuat Toko Makanan: "Roti Enak Bakery"
     */
    private function createFoodStore(Template $template, SubscriptionPackage $package, SubCategory $subCatKueRoti, SubCategory $subCatSnack)
    {
        $user = User::create(['name' => 'Budi Santoso', 'email' => 'mitra3@gmail.com', 'password' => Hash::make('mitra123'), 'phone' => '081122334455', 'status' => 'active', 'alamat' => 'Jl. Roti No. 3, Bandung']);
        $user->assignRole('mitra');

        $shop = Shop::create(['user_id' => $user->id, 'shop_name' => 'Roti Enak Bakery', 'year_founded' => '2019-08-17', 'shop_address' => 'Jl. Braga No. 55, Bandung', 'postal_code' => '40111', 'product_categories' => 'kuliner', 'shop_photo' => $this->getImagePath('shop_photos', 'shop_bakery.jpg'), 'ktp' => $this->getImagePath('documents', 'ktp_dummy.pdf'), 'sku' => $this->getImagePath('documents', 'sku_dummy.pdf'), 'npwp' => $this->getImagePath('documents', 'npwp_dummy.pdf'), 'nib' => $this->getImagePath('documents', 'nib_dummy.pdf'), 'iumk' => $this->getImagePath('documents', 'iumk_dummy.pdf')]);

        $subdomain = Subdomain::create(['user_id' => $user->id, 'shop_id' => $shop->id, 'subdomain_name' => 'rotienak', 'status' => 'active', 'publication_status' => 'published']);

        $orderGroupId = Str::uuid();
        $order = Order::create(['order_number' => 'SUB-' . strtoupper(Str::random(8)), 'user_id' => $user->id, 'shop_id' => $shop->id, 'order_group_id' => $orderGroupId, 'subdomain_id' => $subdomain->id, 'status' => 'completed', 'total_price' => $package->yearly_price, 'subtotal' => $package->yearly_price, 'order_date' => Carbon::now()]);

        Payment::create(['user_id' => $user->id, 'order_id' => $order->id, 'order_group_id' => $orderGroupId, 'subs_package_id' => $package->id, 'midtrans_order_id' => $order->id, 'midtrans_transaction_status' => 'settlement', 'midtrans_payment_type' => 'bank_transfer', 'total_payment' => $package->yearly_price, 'midtrans_response' => json_encode(['transaction_id' => 'TRANS-' . Str::uuid(), 'status_code' => '200', 'status_message' => 'Success, transaction is found'])]);

        UserPackage::create(['user_id' => $user->id, 'subs_package_id' => $package->id, 'plan_type' => 'yearly', 'price_paid' => $package->yearly_price, 'active_date' => Carbon::now(), 'expired_date' => Carbon::now()->addYear(), 'status' => 'active']);

        Tenant::create(['user_id' => $user->id, 'template_id' => $template->id, 'subdomain_id' => $subdomain->id]);

        Contact::create(['user_id' => $user->id, 'phone' => $user->phone, 'email' => 'order@rotienak.com', 'address_line1' => $shop->shop_address, 'city' => 'Bandung']);

        Hero::create(['shop_id' => $shop->id, 'title' => 'Roti Fresh From The Oven', 'subtitle' => 'Promo Beli 2 Gratis 1', 'image' => $this->getImagePath('heroes', 'hero_bakery_1.jpg'), 'button_text' => 'Pesan Sekarang', 'order' => 1, 'is_active' => true]);

        $productsData = [
            ['sub_category_id' => $subCatKueRoti->id, 'name' => 'Croissant Butter', 'is_hot_sale' => true, 'variants' => [['options' => [['name' => 'Topping', 'value' => 'Original']], 'modal' => 10000, 'profit' => 50, 'stock' => 50], ['options' => [['name' => 'Topping', 'value' => 'Keju']], 'modal' => 12000, 'profit' => 50, 'stock' => 30]]],
            ['sub_category_id' => $subCatKueRoti->id, 'name' => 'Roti Sobek Cokelat', 'is_best_seller' => true, 'variants' => [['options' => [['name' => 'Ukuran', 'value' => 'Loyang Kecil']], 'modal' => 25000, 'profit' => 40, 'stock' => 25]]],
            ['sub_category_id' => $subCatSnack->id, 'name' => 'Pastel Daging Asap', 'variants' => [['options' => [['name' => 'Isi', 'value' => 'Daging & Telur']], 'modal' => 8000, 'profit' => 50, 'stock' => 40]]],
            ['sub_category_id' => $subCatKueRoti->id, 'name' => 'Donat Gula Klasik', 'is_new_arrival' => true, 'variants' => [['options' => [['name' => 'Tipe', 'value' => 'Gula Halus']], 'modal' => 5000, 'profit' => 60, 'stock' => 60]]],
            ['sub_category_id' => $subCatSnack->id, 'name' => 'Keripik Singkong Balado', 'variants' => [['options' => [['name' => 'Berat', 'value' => '250g']], 'modal' => 15000, 'profit' => 66, 'stock' => 100]]],
        ];

        foreach ($productsData as $data) {
            $product = Product::create(['user_id' => $user->id, 'shop_id' => $shop->id, 'sub_category_id' => $data['sub_category_id'], 'name' => $data['name'], 'slug' => Str::slug($data['name']) . '-' . uniqid(), 'short_description' => 'Deskripsi singkat untuk ' . $data['name'], 'description' => 'Deskripsi panjang untuk ' . $data['name'], 'main_image' => $this->getImagePath('products', 'product.jpg'), 'gallery_image_paths' => [$this->getImagePath('gallery', 'gallery1.jpg')], 'status' => 'active', 'is_best_seller' => $data['is_best_seller'] ?? false, 'is_new_arrival' => $data['is_new_arrival'] ?? false, 'is_hot_sale' => $data['is_hot_sale'] ?? false]);
            foreach ($data['variants'] as $variantData) {
                $modal = $variantData['modal'];
                $profit = $variantData['profit'];
                $price = $modal * (1 + ($profit / 100));
                $name = collect($variantData['options'])->pluck('value')->implode(' / ');
                Varian::create(['product_id' => $product->id, 'name' => $name, 'price' => $price, 'modal_price' => $modal, 'profit_percentage' => $profit, 'stock' => $variantData['stock'], 'status' => 'active', 'options_data' => $variantData['options']]);
            }
        }

        Voucher::create(['user_id' => $user->id, 'subdomain_id' => $subdomain->id, 'voucher_code' => 'ROTIBARU', 'description' => 'Diskon 20% untuk pelanggan baru.', 'min_spending' => 50000, 'start_date' => Carbon::now(), 'expired_date' => Carbon::now()->addMonths(3), 'discount' => 20, 'is_for_new_customer' => true, 'max_uses_per_customer' => true]);
        Voucher::create(['user_id' => $user->id, 'subdomain_id' => $subdomain->id, 'voucher_code' => 'SARAPANENAK', 'description' => 'Diskon 25% min. belanja Rp 75.000.', 'min_spending' => 75000, 'start_date' => Carbon::now(), 'expired_date' => Carbon::now()->addMonth(), 'discount' => 25, 'is_for_new_customer' => false, 'max_uses_per_customer' => false]);
        Voucher::create(['user_id' => $user->id, 'subdomain_id' => $subdomain->id, 'voucher_code' => 'TEMANNGEMIL', 'description' => 'Diskon 15% untuk semua snack.', 'min_spending' => 40000, 'start_date' => Carbon::now(), 'expired_date' => Carbon::now()->addMonths(6), 'discount' => 15, 'is_for_new_customer' => false, 'max_uses_per_customer' => false]);
    }
}
