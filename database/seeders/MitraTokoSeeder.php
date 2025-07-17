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
            Order::where('user_id', $user->id)->delete();
            Payment::where('user_id', $user->id)->delete();
            $user->shop()->delete();
            $user->subdomain()->delete();
            $user->tenant()->delete();
            $user->userPackage()->delete();
            $user->contact()->delete();
            $user->heroes()->delete();
            $user->banners()->delete();
            $user->products()->delete();
            $user->vouchers()->delete();
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
                // --- PERUBAHAN DI SINI ---
                'shop' => ['shop_name' => 'Toko Busana Kita', 'shop_address' => 'Jl. Pahlawan No. 123, Jakarta', 'postal_code' => '12190', 'product_categories' => 'pakaian-aksesoris'],
                'subdomain' => 'toko-busana-kita',
                'template' => $template1,
                'contact' => ['phone' => '6281298765432', 'email' => 'support@tokomajujaya.com'],
                'heroes' => [
                    ['title' => 'Koleksi Musim Panas', 'subtitle' => 'Diskon Hingga 30%', 'image' => 'template1/img/hero/hero-1.jpg'],
                    ['title' => 'Gaya Kasual Terbaik', 'subtitle' => 'Tampil Beda Setiap Hari', 'image' => 'template1/img/hero/hero-2.jpg'],
                ],
                'banners' => [
                    ['title' => 'Aksesoris Wajib Punya', 'image' => 'template1/img/banner/banner-1.jpg'],
                    ['title' => 'Tas & Dompet Terbaru', 'image' => 'template1/img/banner/banner-2.jpg'],
                ],
                'products' => [
                    ['name' => 'Blouse Wanita Elegan', 'price' => 185000, 'is_best_seller' => true, 'sub_category_id' => $catBajuWanita->id, 'main_image' => 'template1/img/product/product-1.jpg'],
                    ['name' => 'Kemeja Pria Lengan Panjang', 'price' => 220000, 'is_new_arrival' => true, 'sub_category_id' => $catBajuWanita->id, 'main_image' => 'template1/img/product/product-2.jpg'],
                    ['name' => 'Gaun Pesta Malam', 'price' => 350000, 'is_hot_sale' => true, 'sub_category_id' => $catBajuWanita->id, 'main_image' => 'template1/img/product/product-3.jpg'],
                ],
                'vouchers' => [
                    ['code' => 'MAJUJAYA10', 'discount' => 10, 'min_spending' => 150000, 'description' => 'Diskon 10% untuk semua produk.'],
                    ['code' => 'DISKONHEBOH', 'discount' => 25, 'min_spending' => 500000, 'description' => 'Diskon spesial Rp 25% untuk pembelanjaan di atas Rp 500.000.'],
                ]
            ],
            [
                'user' => ['name' => 'Siti Aminah', 'email' => 'mitra2@gmail.com', 'password' => 'mitra123'],
                // --- PERUBAHAN DI SINI ---
                'shop' => ['shop_name' => 'Gaya Modern', 'shop_address' => 'Jl. Merdeka No. 45, Bandung', 'postal_code' => '40117', 'product_categories' => 'pakaian-aksesoris'],
                'subdomain' => 'gaya-modern',
                'template' => $template1,
                'contact' => ['phone' => '6285712345678', 'email' => 'cs@gayamodern.com'],
                'heroes' => [
                    ['title' => 'Denim Never Dies', 'subtitle' => 'Koleksi Jeans Terbaru', 'image' => 'template2/img/hero/hero-a.jpg'],
                ],
                'banners' => [
                    ['title' => 'Diskon Akhir Pekan', 'image' => 'template2/img/banner/banner-a.jpg'],
                ],
                'products' => [
                    ['name' => 'Jaket Denim Pria', 'price' => 299000, 'is_best_seller' => true, 'sub_category_id' => $catAksesoris->id, 'main_image' => 'template2/img/product/product-4.jpg'],
                    ['name' => 'Celana Chino Slim-Fit', 'price' => 199000, 'is_new_arrival' => true, 'sub_category_id' => $catAksesoris->id, 'main_image' => 'template2/img/product/product-5.jpg'],
                ],
                'vouchers' => [
                    ['code' => 'GAYABARU15', 'discount' => 15, 'min_spending' => 200000, 'description' => 'Diskon 15% untuk pelanggan baru.'],
                ]
            ],
            [
                'user' => ['name' => 'Rina Melati', 'email' => 'mitra3@gmail.com', 'password' => 'mitra123'],
                // --- PERUBAHAN DI SINI ---
                'shop' => ['shop_name' => 'Fashionista Corner', 'shop_address' => 'Jl. Sudirman Kav. 5, Surabaya', 'postal_code' => '60271', 'product_categories' => 'pakaian-aksesoris'],
                'subdomain' => 'fashionista-corner',
                'template' => $template1,
                'contact' => ['phone' => '6281122334455', 'email' => 'info@fashionistacorner.id'],
                'heroes' => [
                    ['title' => 'Luxury Handbags', 'subtitle' => 'Edisi Terbatas', 'image' => 'template1/img/hero/hero-3.jpg'],
                ],
                'banners' => [
                    ['title' => 'Sepatu Impian Anda', 'image' => 'template1/img/banner/banner-3.jpg'],
                ],
                'products' => [
                    ['name' => 'Tas Kulit Premium', 'price' => 750000, 'is_hot_sale' => true, 'sub_category_id' => $catAksesoris->id, 'main_image' => 'template1/img/product/product-6.jpg'],
                    ['name' => 'Sepatu Hak Tinggi', 'price' => 450000, 'is_best_seller' => true, 'sub_category_id' => $catSepatu->id, 'main_image' => 'template1/img/product/product-7.jpg'],
                ],
                'vouchers' => [
                    ['code' => 'FASHION5', 'discount' => 5, 'min_spending' => 0, 'description' => 'Diskon 5% tanpa minimum pembelian.'],
                    ['code' => 'CORNER100K', 'discount' => 20, 'min_spending' => 500000, 'description' => 'Diskon 20% untuk pembelanjaan di atas Rp 500.000.'],
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
                $data['heroes'],
                $data['banners'],
                $data['products'],
                $data['vouchers']
            );
        }

        $this->command->info('Seeder untuk 3 Toko Mitra Demo berhasil dijalankan!');
    }

    /**
     * Fungsi privat untuk membuat satu set data lengkap untuk seorang mitra.
     */
    private function createMitraData(array $userData, array $shopData, string $subdomainName, Template $template, SubscriptionPackage $package, array $contactData, array $heroesData, array $bannersData, array $productsData, array $vouchersData)
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

        // 2. Buat Toko (Shop)
        // Tidak perlu perubahan di sini, karena $shopData sudah mengandung postal_code
        $shop = Shop::create(array_merge($shopData, [
            'user_id' => $mitra->id,
            'shop_photo' => 'seeders/shop_photo.jpg',
            'ktp' => 'seeders/ktp.jpg',
        ]));

        // 3. Buat Subdomain
        $subdomain = Subdomain::create(['user_id' => $mitra->id, 'subdomain_name' => $subdomainName, 'status' => 'active']);

        // 4. Buat Tenant
        Tenant::create(['user_id' => $mitra->id, 'subdomain_id' => $subdomain->id, 'template_id' => $template->id]);

        // 5. Buat Paket Langganan Aktif
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

        // 7. Buat Hero Sliders
        foreach ($heroesData as $index => $hero) {
            Hero::create(array_merge($hero, ['user_id' => $mitra->id, 'order' => $index + 1, 'is_active' => true]));
        }

        // 8. Buat Banner Promosi
        foreach ($bannersData as $index => $banner) {
            Banner::create(array_merge($banner, ['user_id' => $mitra->id, 'order' => $index + 1, 'is_active' => true]));
        }

        // 9. Buat Produk
        foreach ($productsData as $productData) {
            $product = Product::create([
                'user_id' => $mitra->id,
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
        }

        // 10. Buat Voucher
        foreach ($vouchersData as $voucher) {
            Voucher::updateOrCreate(
                ['voucher_code' => strtolower($voucher['code'])],
                [
                    'user_id' => $mitra->id,
                    'subdomain_id' => $subdomain->id,
                    'description' => $voucher['description'],
                    'discount' => $voucher['discount'],
                    'min_spending' => $voucher['min_spending'],
                    'start_date' => now(),
                    'expired_date' => now()->addYear(),
                ]
            );
        }
    }
}
