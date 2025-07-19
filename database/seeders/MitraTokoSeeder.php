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
use App\Models\CustomTema;
use App\Models\ShopSetting;
use App\Models\Category; // Import model Category
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
        $emails = ['rian.fashion@example.com', 'clara.chic@example.com', 'chef.anton@example.com'];
        $users = User::whereIn('email', $emails)->get();
        foreach ($users as $user) {
            if ($user->shop) {
                $user->shop->products()->forceDelete();
                $user->shop->heroes()->forceDelete();
                $user->shop->banners()->forceDelete();
                $user->shop->settings()->forceDelete();
                if ($user->customTema) { // Relasi mungkin ada di User atau Shop, periksa keduanya
                    $user->customTema()->forceDelete();
                }
                if ($user->contact) {
                    $user->contact()->forceDelete();
                }
                $user->shop->subdomain()->forceDelete();
                $user->shop->forceDelete();
            }
            $user->tenant()->forceDelete();
            $user->userPackage()->forceDelete();
            $user->orders()->forceDelete();
            $user->payments()->forceDelete();
            $user->vouchers()->forceDelete();
            $user->forceDelete();
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Ambil data master yang akan digunakan bersama
        $businessPackage = SubscriptionPackage::where('package_name', 'Business Plan')->firstOrFail();
        $template1 = Template::where('path', 'template1')->firstOrFail();

        // Gunakan firstOrCreate untuk membuat kategori jika belum ada
        $this->command->info('Membuat kategori & subkategori jika belum ada...');

        // Buat Kategori Utama
        $pakaianCategory = Category::firstOrCreate(
            ['slug' => 'pakaian-aksesoris'],
            ['name' => 'Pakaian & Aksesoris', 'description' => 'Kategori untuk semua jenis pakaian dan aksesoris fashion.']
        );
        $kulinerCategory = Category::firstOrCreate(
            ['slug' => 'kuliner'],
            ['name' => 'Kuliner', 'description' => 'Kategori untuk berbagai jenis makanan dan minuman.']
        );

        // Buat SubKategori
        $catBajuWanita = SubCategory::firstOrCreate(['slug' => 'baju-wanita'], ['name' => 'Baju Wanita', 'category_id' => $pakaianCategory->id]);
        $catBajuPria = SubCategory::firstOrCreate(['slug' => 'baju-pria'], ['name' => 'Baju Pria', 'category_id' => $pakaianCategory->id]);
        $catAksesoris = SubCategory::firstOrCreate(['slug' => 'aksesoris-lainnya'], ['name' => 'Aksesoris Lainnya', 'category_id' => $pakaianCategory->id]);
        $catSepatu = SubCategory::firstOrCreate(['slug' => 'sepatu'], ['name' => 'Sepatu', 'category_id' => $pakaianCategory->id]);
        $catTas = SubCategory::firstOrCreate(['slug' => 'tas'], ['name' => 'Tas', 'category_id' => $pakaianCategory->id]);

        $catMakananBerat = SubCategory::firstOrCreate(['slug' => 'makanan-berat'], ['name' => 'Makanan Berat', 'category_id' => $kulinerCategory->id]);
        $catMinuman = SubCategory::firstOrCreate(['slug' => 'minuman'], ['name' => 'Minuman', 'category_id' => $kulinerCategory->id]);
        $catCamilan = SubCategory::firstOrCreate(['slug' => 'camilan'], ['name' => 'Camilan', 'category_id' => $kulinerCategory->id]);
        $catKue = SubCategory::firstOrCreate(['slug' => 'kue-roti'], ['name' => 'Kue & Roti', 'category_id' => $kulinerCategory->id]);
        $catSambal = SubCategory::firstOrCreate(['slug' => 'bumbu-masak'], ['name' => 'Bumbu Masak', 'category_id' => $kulinerCategory->id]);

        // URL dasar untuk gambar
        $imageUrl = 'https://ecommercegaruda.my.id/storage/';

        // 2. Definisikan data untuk setiap mitra
        $mitraDataArray = [
            // =================================================================
            // MITRA 1: FASHION ETNIK
            // =================================================================
            [
                'user' => ['name' => 'Hilmi Ramadhan', 'email' => 'hilmi21ti@mahasiswa.pcr.ac.id', 'password' => 'mitra123'],
                'shop' => ['shop_name' => 'Gaya Nusantara', 'shop_address' => 'Jl. Jenderal Sudirman No. 25, Jakarta Pusat', 'postal_code' => '10220', 'product_categories' => 'pakaian-aksesoris'],
                'subdomain' => 'gayanusantara',
                'contact' => [
                    'address_line1' => 'Jl. Jenderal Sudirman No. 25, Jakarta Pusat, DKI Jakarta, 10220',
                    'phone' => '081234567890',
                    'email' => 'cs@gayanusantara.com',
                    'working_hours' => "Senin - Jumat: 09:00 - 20:00\nSabtu - Minggu: 10:00 - 18:00",
                    'map_embed_code' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.521209531891!2d106.81938231534957!3d-6.194420195514931!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f41a9c381c51%3A0x1c3a2f6b4c1e4b0!2sPlaza%20Indonesia!5e0!3m2!1sen!2sid!4v1678886300000!5m2!1sen!2sid" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>'
                ],
                'custom_tema' => [
                    'shop_logo' => $imageUrl . 'seeders/logos/gaya-nusantara-logo.png',
                ],
                'shop_settings' => [
                    ['key' => 'theme_color', 'value' => '#8B4513'], // Coklat tua
                ],
                'heroes' => [
                    ['title' => 'Pesona Batik Warisan', 'subtitle' => 'Koleksi Premium Terbaru', 'image' => $imageUrl . 'seeders/heroes/batik-hero.jpg', 'button_text' => 'Lihat Koleksi', 'button_url' => '/shop'],
                    ['title' => 'Keanggunan Tenun Indonesia', 'subtitle' => 'Diskon Spesial 20%', 'image' => $imageUrl . 'seeders/heroes/tenun-hero.jpg', 'button_text' => 'Belanja Sekarang', 'button_url' => '/shop'],
                    ['title' => 'Gaya Kasual Modern', 'subtitle' => 'Nyaman & Tetap Trendi', 'image' => $imageUrl . 'seeders/heroes/casual-hero.jpg', 'button_text' => 'Jelajahi', 'button_url' => '/shop'],
                ],
                'banners' => [
                    ['title' => 'Kemeja Pria', 'image' => $imageUrl . 'seeders/banners/kemeja-pria-banner.jpg', 'link_url' => '/shop?category=baju-pria'],
                    ['title' => 'Dress Wanita', 'image' => $imageUrl . 'seeders/banners/dress-wanita-banner.jpg', 'link_url' => '/shop?category=baju-wanita'],
                    ['title' => 'Aksesoris Etnik', 'image' => $imageUrl . 'seeders/banners/aksesoris-banner.jpg', 'link_url' => '/shop?category=aksesoris-lainnya'],
                    ['title' => 'Sepatu Kulit', 'image' => $imageUrl . 'seeders/banners/sepatu-banner.jpg', 'link_url' => '/shop?category=sepatu'],
                    ['title' => 'Tas Tangan', 'image' => $imageUrl . 'seeders/banners/tas-banner.jpg', 'link_url' => '/shop?category=tas'],
                ],
                'products' => [
                    ['name' => 'Kemeja Batik Pria Lengan Panjang', 'price' => 250000, 'is_best_seller' => true, 'sub_category_id' => $catBajuPria->id, 'main_image' => $imageUrl . 'seeders/products/kemeja-batik.jpg'],
                    ['name' => 'Dress Tenun Wanita Modern', 'price' => 350000, 'is_new_arrival' => true, 'sub_category_id' => $catBajuWanita->id, 'main_image' => $imageUrl . 'seeders/products/dress-tenun.jpg'],
                    ['name' => 'Kalung Etnik Kayu Jati', 'price' => 85000, 'sub_category_id' => $catAksesoris->id, 'main_image' => $imageUrl . 'seeders/products/kalung-etnik.jpg'],
                    ['name' => 'Sepatu Pantofel Kulit Asli', 'price' => 450000, 'sub_category_id' => $catSepatu->id, 'main_image' => $imageUrl . 'seeders/products/sepatu-pantofel.jpg'],
                    ['name' => 'Tas Bahu Anyaman Rotan', 'price' => 180000, 'is_hot_sale' => true, 'sub_category_id' => $catTas->id, 'main_image' => $imageUrl . 'seeders/products/tas-rotan.jpg'],
                ],
                'vouchers' => [
                    ['code' => 'GAYA10', 'discount' => 10, 'min_spending' => 200000, 'description' => 'Diskon 10% untuk semua produk fashion.'],
                    ['code' => 'ONGKIRGRATIS', 'discount' => 15000, 'min_spending' => 300000, 'description' => 'Potongan ongkir Rp 15.000.'],
                    ['code' => 'NUSANTARA50', 'discount' => 50000, 'min_spending' => 500000, 'description' => 'Potongan langsung Rp 50.000.'],
                ]
            ],
            // =================================================================
            // MITRA 2: FASHION MODERN
            // =================================================================
            [
                'user' => ['name' => 'Rachel Jeflisa', 'email' => 'rachel21ti@mahasiswa.pcr.ac.id', 'password' => 'mitra123'],
                'shop' => ['shop_name' => 'Chic & Style Boutique', 'shop_address' => 'Jl. Riau No. 18, Bandung', 'postal_code' => '40115', 'product_categories' => 'pakaian-aksesoris'],
                'subdomain' => 'chicstyle',
                'contact' => [
                    'address_line1' => 'Jl. Riau No. 18, Citarum, Bandung Wetan, Kota Bandung, Jawa Barat, 40115',
                    'phone' => '085712345678',
                    'email' => 'hello@chicstyle.com',
                    'working_hours' => "Setiap Hari: 10:00 - 21:00",
                    'map_embed_code' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3960.902878996167!2d107.6161933153521!3d-6.902201995012501!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e68e64c5e8a753f%3A0x4538a0a5f23e6b19!2sJl.%20L.L.R.E.%20Martadinata%20No.18%2C%20Citarum%2C%20Kec.%20Bandung%20Wetan%2C%20Kota%20Bandung%2C%20Jawa%20Barat%2040115!5e0!3m2!1sen!2sid!4v1678886400000!5m2!1sen!2sid" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>'
                ],
                'custom_tema' => [
                    'shop_logo' => $imageUrl . 'seeders/logos/chic-style-logo.png',
                ],
                'shop_settings' => [
                    ['key' => 'theme_color', 'value' => '#333333'], // Hitam lembut
                ],
                'heroes' => [
                    ['title' => 'Minimalist Wardrobe', 'subtitle' => 'Esensi Gaya Modern', 'image' => $imageUrl . 'seeders/heroes/minimalist-hero.jpg', 'button_text' => 'Jelajahi', 'button_url' => '/shop'],
                    ['title' => 'Koleksi Musim Semi', 'subtitle' => 'Warna-Warna Cerah Terbaru', 'image' => $imageUrl . 'seeders/heroes/spring-hero.jpg', 'button_text' => 'Lihat Produk', 'button_url' => '/shop'],
                    ['title' => 'Flash Sale Akhir Pekan', 'subtitle' => 'Diskon Hingga 50%!', 'image' => $imageUrl . 'seeders/heroes/sale-hero.jpg', 'button_text' => 'Belanja Sekarang', 'button_url' => '/shop'],
                ],
                'banners' => [
                    ['title' => 'Blouse Wanita', 'image' => $imageUrl . 'seeders/banners/blouse-banner.jpg', 'link_url' => '/shop?category=baju-wanita'],
                    ['title' => 'Celana Pria', 'image' => $imageUrl . 'seeders/banners/celana-pria-banner.jpg', 'link_url' => '/shop?category=baju-pria'],
                    ['title' => 'Tas Selempang', 'image' => $imageUrl . 'seeders/banners/tas-selempang-banner.jpg', 'link_url' => '/shop?category=tas'],
                    ['title' => 'Sneakers Putih', 'image' => $imageUrl . 'seeders/banners/sneakers-banner.jpg', 'link_url' => '/shop?category=sepatu'],
                    ['title' => 'Promo Beli 1 Gratis 1', 'image' => $imageUrl . 'seeders/banners/bogo-banner.jpg', 'link_url' => '/shop'],
                ],
                'products' => [
                    ['name' => 'Blouse Sutra Wanita Kerah Pita', 'price' => 220000, 'is_new_arrival' => true, 'sub_category_id' => $catBajuWanita->id, 'main_image' => $imageUrl . 'seeders/products/blouse-sutra.jpg'],
                    ['name' => 'Celana Chino Pria Slim Fit', 'price' => 275000, 'is_best_seller' => true, 'sub_category_id' => $catBajuPria->id, 'main_image' => $imageUrl . 'seeders/products/celana-chino.jpg'],
                    ['name' => 'Tas Selempang Kanvas Unisex', 'price' => 150000, 'sub_category_id' => $catTas->id, 'main_image' => $imageUrl . 'seeders/products/tas-kanvas.jpg'],
                    ['name' => 'Sneakers Kanvas Putih Klasik', 'price' => 320000, 'sub_category_id' => $catSepatu->id, 'main_image' => $imageUrl . 'seeders/products/sneakers-putih.jpg'],
                    ['name' => 'Scarf Polos Bahan Premium', 'price' => 95000, 'sub_category_id' => $catAksesoris->id, 'main_image' => $imageUrl . 'seeders/products/scarf-polos.jpg'],
                ],
                'vouchers' => [
                    ['code' => 'CHIC15', 'discount' => 15, 'min_spending' => 250000, 'description' => 'Diskon 15% untuk koleksi terbaru.'],
                    ['code' => 'NEWLOOK', 'discount' => 50000, 'min_spending' => 300000, 'description' => 'Potongan Rp 50.000 untuk pelanggan baru.'],
                    ['code' => 'WEEKENDDEAL', 'discount' => 20, 'min_spending' => 400000, 'description' => 'Diskon 20% khusus Sabtu & Minggu.'],
                ]
            ],
            // =================================================================
            // MITRA 3: KULINER
            // =================================================================
            [
                'user' => ['name' => 'Chef Anton', 'email' => 'chef.anton@example.com', 'password' => 'mitra123'],
                'shop' => ['shop_name' => 'Dapur Lezat Nusantara', 'shop_address' => 'Jl. Malioboro No. 50, Yogyakarta', 'postal_code' => '55213', 'product_categories' => 'kuliner'],
                'subdomain' => 'dapurlezat',
                'contact' => [
                    'address_line1' => 'Jl. Malioboro No. 50, Sosromenduran, Gedong Tengen, Kota Yogyakarta, DIY, 55213',
                    'phone' => '087755501234',
                    'email' => 'pesan@dapurlezat.id',
                    'working_hours' => "Senin - Minggu: 08:00 - 22:00",
                    'map_embed_code' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3952.903907578238!2d110.3631983153571!3d-7.79971899437905!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a5788a5e5c5d7%3A0x1d3d3a33a4e9e1c!2sJl.%20Malioboro%2C%20Kota%20Yogyakarta%2C%20Daerah%20Istimewa%20Yogyakarta!5e0!3m2!1sen!2sid!4v1678886500000!5m2!1sen!2sid" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>'
                ],
                'custom_tema' => [
                    'shop_logo' => $imageUrl . 'seeders/logos/dapur-lezat-logo.png',
                ],
                'shop_settings' => [
                    ['key' => 'theme_color', 'value' => '#E53E3E'], // Merah
                ],
                'heroes' => [
                    ['title' => 'Cita Rasa Asli Indonesia', 'subtitle' => 'Resep Warisan Keluarga', 'image' => $imageUrl . 'seeders/heroes/rendang-hero.jpg', 'button_text' => 'Lihat Menu', 'button_url' => '/shop'],
                    ['title' => 'Paket Makan Siang Hemat', 'subtitle' => 'Mulai dari Rp 25.000', 'image' => $imageUrl . 'seeders/heroes/paket-nasi-hero.jpg', 'button_text' => 'Pesan Sekarang', 'button_url' => '/shop'],
                    ['title' => 'Pesan Antar, Gratis Ongkir!', 'subtitle' => 'Untuk Area Yogyakarta', 'image' => $imageUrl . 'seeders/heroes/delivery-hero.jpg', 'button_text' => 'Hubungi Kami', 'button_url' => '/contact'],
                ],
                'banners' => [
                    ['title' => 'Nasi Kotak Spesial', 'image' => $imageUrl . 'seeders/banners/nasi-kotak-banner.jpg', 'link_url' => '/shop?category=makanan-berat'],
                    ['title' => 'Aneka Sambal Pedas', 'image' => $imageUrl . 'seeders/banners/sambal-banner.jpg', 'link_url' => '/shop?category=bumbu-masak'],
                    ['title' => 'Minuman Segar', 'image' => $imageUrl . 'seeders/banners/minuman-banner.jpg', 'link_url' => '/shop?category=minuman'],
                    ['title' => 'Jajanan Pasar', 'image' => $imageUrl . 'seeders/banners/jajanan-banner.jpg', 'link_url' => '/shop?category=camilan'],
                    ['title' => 'Kue Tampah Acara', 'image' => $imageUrl . 'seeders/banners/kue-tampah-banner.jpg', 'link_url' => '/shop?category=kue-roti'],
                ],
                'products' => [
                    ['name' => 'Nasi Rendang Daging Sapi Komplit', 'price' => 35000, 'is_best_seller' => true, 'sub_category_id' => $catMakananBerat->id, 'main_image' => $imageUrl . 'seeders/products/nasi-rendang.jpg'],
                    ['name' => 'Es Cendol Durian Medan', 'price' => 18000, 'sub_category_id' => $catMinuman->id, 'main_image' => $imageUrl . 'seeders/products/es-cendol.jpg'],
                    ['name' => 'Risoles Ragout Ayam (Isi 5)', 'price' => 25000, 'is_new_arrival' => true, 'sub_category_id' => $catCamilan->id, 'main_image' => $imageUrl . 'seeders/products/risoles.jpg'],
                    ['name' => 'Bolu Kukus Gula Merah', 'price' => 45000, 'sub_category_id' => $catKue->id, 'main_image' => $imageUrl . 'seeders/products/bolu-kukus.jpg'],
                    ['name' => 'Sambal Bawang Botol Premium', 'price' => 30000, 'is_hot_sale' => true, 'sub_category_id' => $catSambal->id, 'main_image' => $imageUrl . 'seeders/products/sambal-bawang.jpg'],
                ],
                'vouchers' => [
                    ['code' => 'MAKANENAK10', 'discount' => 10, 'min_spending' => 100000, 'description' => 'Diskon 10% untuk semua menu makanan.'],
                    ['code' => 'PESANANTAR', 'discount' => 10000, 'min_spending' => 150000, 'description' => 'Gratis ongkir, maks. potongan Rp 10.000.'],
                    ['code' => 'HEMAT25', 'discount' => 25000, 'min_spending' => 200000, 'description' => 'Potongan langsung Rp 25.000.'],
                ]
            ],
        ];

        // 3. Loop dan buat data untuk setiap mitra
        foreach ($mitraDataArray as $data) {
            $this->createMitraData(
                $data,
                $template1,
                $businessPackage
            );
        }

        $this->command->info('Seeder untuk 3 Toko Mitra Demo berhasil dijalankan!');
    }

    /**
     * Fungsi privat untuk membuat satu set data lengkap untuk seorang mitra.
     */
    private function createMitraData(array $data, Template $template, SubscriptionPackage $package)
    {
        // 1. Buat User Mitra
        $mitra = User::create([
            'name' => $data['user']['name'],
            'email' => $data['user']['email'],
            'password' => Hash::make($data['user']['password']),
            'phone' => $data['contact']['phone'],
            'position' => 'Pemilik Usaha',
            'status' => 'active',
        ]);
        $mitra->assignRole('mitra');

        // 2. Buat Toko (Shop)
        $shop = Shop::create(array_merge($data['shop'], [
            'user_id' => $mitra->id,
            'shop_photo' => 'seeders/defaults/shop_photo.jpg',
            'ktp' => 'seeders/defaults/ktp.jpg',
        ]));

        // 3. Buat Subdomain
        $subdomain = Subdomain::create([
            'user_id' => $mitra->id,
            'shop_id' => $shop->id,
            'subdomain_name' => $data['subdomain'],
            'status' => 'active'
        ]);

        // 4. Buat Tenant
        Tenant::create([
            'user_id' => $mitra->id,
            'template_id' => $template->id,
            'subdomain_id' => $subdomain->id,
        ]);

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
        $order = Order::create(['user_id' => $mitra->id, 'status' => 'completed', 'order_date' => now(), 'total_price' => $userPackage->price_paid]);
        Payment::create(['user_id' => $mitra->id, 'order_id' => $order->id, 'subs_package_id' => $package->id, 'midtrans_order_id' => 'SUB-' . Str::uuid(), 'midtrans_transaction_status' => 'settlement', 'midtrans_payment_type' => 'bank_transfer', 'total_payment' => $userPackage->price_paid]);

        // 6. Buat Data Kontak Toko
        Contact::create(array_merge($data['contact'], ['user_id' => $mitra->id]));

        // 7. PERBAIKAN: Buat Custom Tema dan Shop Settings
        CustomTema::create(array_merge($data['custom_tema'], [
            'user_id' => $mitra->id,
            'subdomain_id' => $subdomain->id,
            'shop_name' => $shop->shop_name,
            'shop_logo' => $shop->shop_logo,
        ]));

        foreach ($data['shop_settings'] as $setting) {
            ShopSetting::create([
                'shop_id' => $shop->id,
                'key' => $setting['key'],
                'value' => $setting['value'],
            ]);
        }

        // 8. Buat Hero Sliders
        foreach ($data['heroes'] as $index => $hero) {
            Hero::create(array_merge($hero, ['shop_id' => $shop->id, 'order' => $index + 1, 'is_active' => true]));
        }

        // 9. Buat Banner Promosi
        foreach ($data['banners'] as $index => $banner) {
            Banner::create(array_merge($banner, ['shop_id' => $shop->id, 'order' => $index + 1, 'is_active' => true]));
        }

        // 10. Buat Produk
        $createdProducts = [];
        foreach ($data['products'] as $productData) {
            $product = Product::create([
                
                'shop_id' => $shop->id,
                'name' => $productData['name'],
                'slug' => Str::slug($productData['name']) . '-' . uniqid(),
                'short_description' => 'Deskripsi singkat yang menarik untuk ' . $productData['name'],
                'description' => 'Deskripsi lengkap dan detail mengenai ' . $productData['name'] . ', menjelaskan bahan, ukuran, dan keunggulannya.',
                'price' => $productData['price'],
                'user_id' => $mitra->id,
                'sub_category_id' => $productData['sub_category_id'],
                'main_image' => $productData['main_image'],
                'is_best_seller' => $productData['is_best_seller'] ?? false,
                'is_new_arrival' => $productData['is_new_arrival'] ?? false,
                'is_hot_sale' => $productData['is_hot_sale'] ?? false,
                'status' => 'active',
            ]);
            // Buat varian default
            $product->variants()->create(['color' => 'Merah', 'size' => 'M', 'stock' => 20]);
            $product->variants()->create(['color' => 'Biru', 'size' => 'L', 'stock' => 15]);
            $product->variants()->create(['color' => 'Hitam', 'size' => 'All Size', 'stock' => 25]);
            $createdProducts[] = $product;
        }

        // 11. Buat Voucher
        foreach ($data['vouchers'] as $voucherData) {
            Voucher::create([
                'user_id' => $mitra->id,
                'subdomain_id' => $subdomain->id,
                'voucher_code' => strtoupper($voucherData['code']),
                'description' => $voucherData['description'],
                'discount' => $voucherData['discount'],
                'min_spending' => $voucherData['min_spending'],
                'start_date' => now(),
                'expired_date' => now()->addYear(),
            ]);
        }
    }
}
