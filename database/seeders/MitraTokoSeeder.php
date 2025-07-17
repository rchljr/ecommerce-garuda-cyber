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
use App\Models\ShopSetting;
use App\Models\SubCategory;
use App\Models\Voucher;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class MitraTokoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Hapus data lama untuk menghindari duplikasi saat seeder dijalankan ulang
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $emails = ['mitra1@gmail.com', 'mitra2@gmail.com', 'mitra3@gmail.com'];
        $users = User::whereIn('email', $emails)->get();
        foreach ($users as $user) {
            $user->forceDelete();
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Ambil data master yang akan digunakan bersama
        $businessPackage = SubscriptionPackage::where('package_name', 'Business Plan')->first();
        $template1 = Template::where('path', 'template1')->first();
        $template2 = Template::where('path', 'template2')->first();
        $catBajuWanita = SubCategory::where('slug', 'baju-wanita')->first();
        $catAksesoris = SubCategory::where('slug', 'aksesoris-lainnya')->first();
        $catSepatu = SubCategory::where('slug', 'sepatu')->first();

        if (!$businessPackage || !$template1 || !$template2 || !$catBajuWanita || !$catAksesoris || !$catSepatu) {
            $this->command->error('Pastikan SubscriptionPackage, Template (1&2), dan SubCategory (Baju Wanita, Aksesoris, Sepatu) sudah ada di database.');
            return;
        }

        // 2. Definisikan data untuk setiap mitra
        $mitraDataArray = [
            [
                'user' => ['name' => 'Budi Santoso', 'email' => 'mitra1@gmail.com', 'password' => 'mitra123'],
                'shop' => ['shop_name' => 'Toko Busana Kita', 'shop_address' => 'Jl. Pahlawan No. 123, Jakarta', 'postal_code' => '12190', 'product_categories' => 'pakaian-aksesoris'],
                'subdomain' => 'toko-busana-kita',
                'template' => $template1,
                'contact' => ['phone' => '6281298765432', 'email' => 'support@tokomajujaya.com'],
                'shop_settings' => [
                    ['key' => 'theme_color', 'value' => '#E53E3E'],
                    ['key' => 'logo_url', 'value' => 'seeders/logo1.png'],
                ],
                'heroes' => [
                    ['title' => 'Koleksi Musim Panas', 'subtitle' => 'Diskon Hingga 30%', 'image' => 'template1/img/hero/hero-1.jpg'],
                ],
                'banners' => [
                    ['title' => 'Aksesoris Wajib Punya', 'image' => 'template1/img/banner/banner-1.jpg'],
                ],
                'products' => [
                    ['name' => 'Blouse Wanita Elegan', 'price' => 185000, 'is_best_seller' => true, 'sub_category_id' => $catBajuWanita->id, 'main_image' => 'template1/img/product/product-1.jpg'],
                ],
                'vouchers' => [
                    ['code' => 'MAJUJAYA10', 'discount' => 10, 'min_spending' => 150000, 'description' => 'Diskon 10% untuk semua produk.', 'product_indices' => [0]],
                ]
            ],
            [
                'user' => ['name' => 'Siti Aminah', 'email' => 'mitra2@gmail.com', 'password' => 'mitra123'],
                'shop' => ['shop_name' => 'Gaya Modern', 'shop_address' => 'Jl. Merdeka No. 45, Bandung', 'postal_code' => '40117', 'product_categories' => 'pakaian-aksesoris'],
                'subdomain' => 'gaya-modern',
                'template' => $template1,
                'contact' => ['phone' => '6285712345678', 'email' => 'cs@gayamodern.com'],
                 'shop_settings' => [
                    ['key' => 'theme_color', 'value' => '#3182CE'],
                    ['key' => 'logo_url', 'value' => 'seeders/logo2.png'],
                ],
                'heroes' => [
                    ['title' => 'Denim Never Dies', 'subtitle' => 'Koleksi Jeans Terbaru', 'image' => 'template2/img/hero/hero-a.jpg'],
                ],
                'banners' => [
                    ['title' => 'Diskon Akhir Pekan', 'image' => 'template2/img/banner/banner-a.jpg'],
                ],
                'products' => [
                    ['name' => 'Jaket Denim Pria', 'price' => 299000, 'is_best_seller' => true, 'sub_category_id' => $catAksesoris->id, 'main_image' => 'template2/img/product/product-4.jpg'],
                ],
                'vouchers' => [
                    ['code' => 'GAYABARU15', 'discount' => 15, 'min_spending' => 200000, 'description' => 'Diskon 15% untuk pelanggan baru.', 'product_indices' => [0]],
                ]
            ],
        ];

        // 3. Loop dan buat data untuk setiap mitra
        foreach ($mitraDataArray as $data) {
            $this->createMitraData(
                $data['user'],
                $data['shop'],
                $data['subdomain'],
                $data['template'],
                $businessPackage,
                $data['contact'],
                $data['shop_settings'],
                $data['heroes'],
                $data['banners'],
                $data['products'],
                $data['vouchers']
            );
        }

        $this->command->info('Seeder untuk Toko Mitra Demo berhasil dijalankan!');
    }

    /**
     * Fungsi privat untuk membuat satu set data lengkap untuk seorang mitra.
     */
    private function createMitraData(array $userData, array $shopData, string $subdomainName, Template $template, SubscriptionPackage $package, array $contactData, array $settingsData, array $heroesData, array $bannersData, array $productsData, array $vouchersData)
    {
        // 1. Buat User Mitra
        $mitra = User::create([
            'name' => $userData['name'],
            'email' => $userData['email'],
            'password' => Hash::make($userData['password']),
            'phone' => $contactData['phone'],
            'position' => 'Pemilik Usaha',
            'status' => 'active',
        ]);
        $mitra->assignRole('mitra');

        // 2. Buat Toko (Shop) terlebih dahulu
        $shop = Shop::create(array_merge($shopData, [
            'user_id' => $mitra->id,
            // 'tenant_id' => $tenant->id, // Dihapus sesuai permintaan
            'shop_photo' => 'seeders/shop_photo.jpg',
            'ktp' => 'seeders/ktp.jpg',
        ]));

        // 3. Buat Subdomain, sekarang terhubung ke Shop
        $subdomain = Subdomain::create([
            'user_id' => $mitra->id,
            'shop_id' => $shop->id, // Ditambahkan sesuai permintaan
            'subdomain_name' => $subdomainName,
            'status' => 'active'
        ]);

        // 4. Buat Tenant, dengan subdomain_id yang sudah ada
        $tenant = Tenant::create([
            'user_id' => $mitra->id,
            'template_id' => $template->id,
            'subdomain_id' => $subdomain->id,
        ]);

        // 5. Buat Paket Langganan Aktif dan data Order/Payment terkait
        $userPackage = UserPackage::create([
            'user_id' => $mitra->id,
            'subs_package_id' => $package->id,
            'plan_type' => 'yearly',
            'price_paid' => $package->yearly_price,
            'active_date' => now(),
            'expired_date' => now()->addYear(),
            'status' => 'active',
        ]);
        $order = Order::create([
            'user_id' => $mitra->id,
            'subdomain_id' => null,
            'status' => 'completed',
            'order_date' => now(),
            'total_price' => $userPackage->price_paid,
        ]);
        Payment::create([
            'user_id' => $mitra->id,
            'order_id' => $order->id,
            'subs_package_id' => $package->id,
            'midtrans_order_id' => 'SUB-' . $mitra->id . '-' . time(),
            'midtrans_transaction_status' => 'settlement',
            'midtrans_payment_type' => 'bank_transfer',
            'total_payment' => $userPackage->price_paid,
        ]);

        // 6. Buat Data Kontak Toko
        Contact::create(array_merge($contactData, ['user_id' => $mitra->id]));

        // 7. Buat Shop Settings
        foreach ($settingsData as $setting) {
            ShopSetting::create([
                'shop_id' => $shop->id,
                'key' => $setting['key'],
                'value' => $setting['value'],
            ]);
        }

        // 8. Buat Hero Sliders, sekarang terhubung ke Shop
        foreach ($heroesData as $index => $hero) {
            Hero::create(array_merge($hero, [
                'user_id' => $mitra->id,
                'shop_id' => $shop->id, // Ditambahkan sesuai permintaan
                'order' => $index + 1,
                'is_active' => true
            ]));
        }

        // 9. Buat Banner Promosi, sekarang terhubung ke Shop
        foreach ($bannersData as $index => $banner) {
            Banner::create(array_merge($banner, [
                'user_id' => $mitra->id,
                'shop_id' => $shop->id, // Ditambahkan sesuai permintaan
                'order' => $index + 1,
                'is_active' => true
            ]));
        }

        // 10. Buat Produk, sekarang terhubung ke Shop
        $createdProducts = [];
        foreach ($productsData as $productData) {
            $product = Product::create([
                'user_id' => $mitra->id,
                'shop_id' => $shop->id, // Ditambahkan sesuai permintaan
                'name' => $productData['name'],
                'slug' => Str::slug($productData['name']) . '-' . uniqid(),
                'short_description' => 'Deskripsi singkat untuk ' . $productData['name'],
                'price' => $productData['price'],
                'sub_category_id' => $productData['sub_category_id'],
                'main_image' => $productData['main_image'],
                'is_best_seller' => $productData['is_best_seller'] ?? false,
                'is_new_arrival' => $productData['is_new_arrival'] ?? false,
                'is_hot_sale' => $productData['is_hot_sale'] ?? false,
                'status' => 'active',
            ]);
            $product->variants()->create(['color' => 'Merah', 'size' => 'M', 'stock' => 10]);
            $product->variants()->create(['color' => 'Biru', 'size' => 'L', 'stock' => 15]);
            $createdProducts[] = $product;
        }

        // 11. Buat Voucher dan hubungkan ke Produk
        foreach ($vouchersData as $voucherData) {
            $voucher = Voucher::create([
                'user_id' => $mitra->id,
                'subdomain_id' => $subdomain->id,
                'voucher_code' => strtolower($voucherData['code']),
                'description' => $voucherData['description'],
                'discount' => $voucherData['discount'],
                'min_spending' => $voucherData['min_spending'],
                'start_date' => now(),
                'expired_date' => now()->addYear(),
            ]);

            if (!empty($voucherData['product_indices'])) {
                $productIdsToAttach = [];
                foreach ($voucherData['product_indices'] as $index) {
                    if (isset($createdProducts[$index])) {
                        $productIdsToAttach[] = $createdProducts[$index]->id;
                    }
                }
                $voucher->products()->attach($productIdsToAttach);
            }
        }
    }
}
