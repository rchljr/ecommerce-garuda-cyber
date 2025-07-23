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
use App\Models\Varian; // <-- IMPORT MODEL VARIAN
use App\Models\CustomTema;
use App\Models\ShopSetting;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Voucher;
use App\Models\Customer; // Import model Customer
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

        // Daftar email mitra yang akan dihapus
        $mitraEmailsToDelete = ['hilmi21ti@mahasiswa.pcr.ac.id', 'rachel21ti@mahasiswa.pcr.ac.id', 'chef.anton@example.com'];
        $usersToDelete = User::whereIn('email', $mitraEmailsToDelete)->with('products.variants')->get();

        foreach ($usersToDelete as $user) {
            // Hapus pesanan dan item pesanan terkait (jika ada)
            if ($user->orders) {
                foreach ($user->orders as $order) {
                    $order->items()->forceDelete();
                }
                $user->orders()->forceDelete();
            }

            // Hapus produk yang dimiliki user ini
            // Ini akan menghapus varian terkait secara otomatis jika onDelete('cascade') di relasi Product
            $user->products()->forceDelete();

            // Hapus voucher yang dimiliki user ini
            $user->vouchers()->forceDelete();

            // Hapus data toko dan terkait lainnya
            if ($user->shop) {
                $user->shop->heroes()->forceDelete();
                $user->shop->banners()->forceDelete();
                $user->shop->settings()->forceDelete();
                if ($user->customTema) {
                    $user->customTema()->forceDelete();
                }
                if ($user->contact) {
                    $user->contact()->forceDelete();
                }
                if ($user->shop->subdomain) {
                    $user->shop->subdomain()->forceDelete();
                }
                $user->shop->forceDelete();
            }
            $user->tenant()->forceDelete();
            $user->userPackage()->forceDelete();
            $user->payments()->forceDelete();
            $user->forceDelete();
        }

        // Hapus pelanggan spesifik yang dibuat di seeder ini
        User::where('email', 'customer@gmail.com')->forceDelete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->command->info('Data lama mitra dan pelanggan dummy dibersihkan.');


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

        // 2. Buat satu Pelanggan (Customer) spesifik yang akan digunakan oleh OrderSeeder
        User::firstOrCreate(
            ['email' => 'customer@gmail.com'],
            [
                'name' => 'Pelanggan Setia',
                'password' => Hash::make('customer123'),
                'phone' => '6281211112222',
                'email_verified_at' => now(),
                'status' => 'active',
            ]
        );
        $this->command->info('Pelanggan spesifik customer@gmail.com dibuat/diperbarui.');


        // 3. Definisikan data untuk setiap mitra
        $mitraDataArray = [
            // =================================================================
            // MITRA 1: FASHION ETNIK
            // =================================================================
            [
                'user' => ['name' => 'Hilmi Ramadhan', 'email' => 'hilmi21ti@mahasiswa.pcr.ac.id', 'password' => 'mitra123'],
                'shop' => ['shop_name' => 'Gaya Nusantara', 'shop_address' => 'Jl. Jenderal Sudirman No. 25, Jakarta Pusat', 'postal_code' => '10220', 'product_categories' => 'pakaian-aksesoris'],
                'subdomain_name' => 'gayanusantara',
                'contact' => [
                    'address_line1' => 'Jl. Jenderal Sudirman No. 25, Jakarta Pusat, DKI Jakarta, 10220',
                    'phone' => '081234567890',
                    'email' => 'cs@gayanusantara.com',
                    'working_hours' => "Senin - Jumat: 09:00 - 20:00\nSabtu - Minggu: 10:00 - 18:00",
                    'map_embed_code' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.521209531891!2d106.81938231534957!3d-6.194420195514931!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f41a9c381c51%3A0x1c3a2f6b4c1e4b0!2sPlaza%20Indonesia!5e0!3m2!1sen!2sid!4v1678886300000!5m2!1sen!2sid" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>'
                ],
                'custom_tema' => [
                    'shop_logo' => 'seeders/logos/gaya-nusantara-logo.png',
                    'primary_color' => '#8B4513',
                    'secondary_color' => '#6c757d',
                ],
                'shop_settings' => [
                    ['key' => 'theme_color', 'value' => '#8B4513'],
                ],
                'heroes' => [
                    ['title' => 'Pesona Batik Warisan', 'subtitle' => 'Koleksi Premium Terbaru', 'image' => 'seeders/heroes/batik-hero.jpg', 'button_text' => 'Lihat Koleksi', 'button_url' => '/shop'],
                    ['title' => 'Keanggunan Tenun Indonesia', 'subtitle' => 'Diskon Spesial 20%', 'image' => 'seeders/heroes/tenun-hero.jpg', 'button_text' => 'Belanja Sekarang', 'button_url' => '/shop'],
                ],
                'banners' => [
                    ['title' => 'Kemeja Pria', 'image' => 'seeders/banners/kemeja-pria-banner.jpg', 'link_url' => '/shop?category=baju-pria'],
                    ['title' => 'Dress Wanita', 'image' => 'seeders/banners/dress-wanita-banner.jpg', 'link_url' => '/shop?category=baju-wanita'],
                ],
                'products' => [
                    [
                        'name' => 'Kemeja Batik Pria Lengan Panjang',
                        'main_image' => 'seeders/products/kemeja-batik.jpg',
                        'is_best_seller' => true,
                        'sub_category_id' => $catBajuPria->id,
                        'varians' => [ // Definisi varian untuk produk ini
                            [
                                'options_data' => [['name' => 'Warna', 'value' => 'Merah'], ['name' => 'Ukuran', 'value' => 'S']],
                                'modal_price' => 200000,
                                'profit_percentage' => 25,
                                'stock' => 10,
                            ],
                            [
                                'options_data' => [['name' => 'Warna', 'value' => 'Merah'], ['name' => 'Ukuran', 'value' => 'M']],
                                'modal_price' => 210000,
                                'profit_percentage' => 25,
                                'stock' => 15,
                            ],
                            [
                                'options_data' => [['name' => 'Warna', 'value' => 'Biru'], ['name' => 'Ukuran', 'value' => 'L']],
                                'modal_price' => 220000,
                                'profit_percentage' => 25,
                                'stock' => 12,
                            ],
                        ]
                    ],
                    [
                        'name' => 'Dress Tenun Wanita Modern',
                        'main_image' => 'seeders/products/dress-tenun.jpg',
                        'is_new_arrival' => true,
                        'sub_category_id' => $catBajuWanita->id,
                        'varians' => [
                            [
                                'options_data' => [['name' => 'Warna', 'value' => 'Jingga'], ['name' => 'Ukuran', 'value' => 'S']],
                                'modal_price' => 280000,
                                'profit_percentage' => 25,
                                'stock' => 8,
                            ],
                            [
                                'options_data' => [['name' => 'Warna', 'value' => 'Hijau'], ['name' => 'Ukuran', 'value' => 'M']],
                                'modal_price' => 290000,
                                'profit_percentage' => 25,
                                'stock' => 10,
                            ],
                        ]
                    ],
                    [
                        'name' => 'Kalung Etnik Kayu Jati',
                        'main_image' => 'seeders/products/kalung-etnik.jpg',
                        'sub_category_id' => $catAksesoris->id,
                        'varians' => [
                            [
                                'options_data' => [['name' => 'Warna Kayu', 'value' => 'Coklat Tua']],
                                'modal_price' => 68000,
                                'profit_percentage' => 25,
                                'stock' => 30,
                            ],
                            [
                                'options_data' => [['name' => 'Warna Kayu', 'value' => 'Coklat Muda']],
                                'modal_price' => 65000,
                                'profit_percentage' => 25,
                                'stock' => 25,
                            ],
                        ]
                    ],
                    [
                        'name' => 'Sepatu Pantofel Kulit Asli',
                        'main_image' => 'seeders/products/sepatu-pantofel.jpg',
                        'sub_category_id' => $catSepatu->id,
                        'varians' => [
                            [
                                'options_data' => [['name' => 'Ukuran', 'value' => '39'], ['name' => 'Warna', 'value' => 'Hitam']],
                                'modal_price' => 360000,
                                'profit_percentage' => 25,
                                'stock' => 5,
                            ],
                            [
                                'options_data' => [['name' => 'Ukuran', 'value' => '40'], ['name' => 'Warna', 'value' => 'Hitam']],
                                'modal_price' => 360000,
                                'profit_percentage' => 25,
                                'stock' => 7,
                            ],
                            [
                                'options_data' => [['name' => 'Ukuran', 'value' => '41'], ['name' => 'Warna', 'value' => 'Coklat']],
                                'modal_price' => 370000,
                                'profit_percentage' => 25,
                                'stock' => 6,
                            ],
                        ]
                    ],
                    [
                        'name' => 'Tas Bahu Anyaman Rotan',
                        'main_image' => 'seeders/products/tas-rotan.jpg',
                        'is_hot_sale' => true,
                        'sub_category_id' => $catTas->id,
                        'varians' => [
                            [
                                'options_data' => [['name' => 'Warna', 'value' => 'Natural']],
                                'modal_price' => 120000,
                                'profit_percentage' => 50,
                                'stock' => 15,
                            ],
                            [
                                'options_data' => [['name' => 'Warna', 'value' => 'Coklat Gelap']],
                                'modal_price' => 125000,
                                'profit_percentage' => 50,
                                'stock' => 10,
                            ],
                        ]
                    ],
                ],
                'vouchers' => [
                    ['code' => 'GAYA10', 'discount' => 10, 'min_spending' => 200000, 'description' => 'Diskon 10% untuk semua produk fashion.'],
                ]
            ],
            // =================================================================
            // MITRA 2: FASHION MODERN
            // =================================================================
            [
                'user' => ['name' => 'Rachel Jeflisa', 'email' => 'rachel21ti@mahasiswa.pcr.ac.id', 'password' => 'mitra123'],
                'shop' => ['shop_name' => 'Chic & Style Boutique', 'shop_address' => 'Jl. Riau No. 18, Bandung', 'postal_code' => '40115', 'product_categories' => 'pakaian-aksesoris'],
                'subdomain_name' => 'chicstyle',
                'contact' => [
                    'address_line1' => 'Jl. Riau No. 18, Citarum, Bandung Wetan, Kota Bandung, Jawa Barat, 40115',
                    'phone' => '085712345678',
                    'email' => 'hello@chicstyle.com',
                    'working_hours' => "Setiap Hari: 10:00 - 21:00",
                    'map_embed_code' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3960.902878996167!2d107.6161933153521!3d-6.902201995012501!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e68e64c5e8a753f%3A0x4538a0a5f23e6b19!2sJl.%20L.L.R.E.%20Martadinata%20No.18%2C%20Citarum%2C%20Kec.%20Bandung%20Wetan%2C%20Kota%20Bandung%2C%20Jawa%20Barat%2040115!5e0!3m2!1sen!2sid!4v1678886400000!5m2!1sen!2sid" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>'
                ],
                'custom_tema' => [
                    'shop_logo' => 'seeders/logos/chic-style-logo.png',
                    'primary_color' => '#333333',
                    'secondary_color' => '#6c757d',
                ],
                'shop_settings' => [
                    ['key' => 'theme_color', 'value' => '#333333'],
                ],
                'heroes' => [
                    ['title' => 'Minimalist Wardrobe', 'subtitle' => 'Esensi Gaya Modern', 'image' => 'seeders/heroes/minimalist-hero.jpg', 'button_text' => 'Jelajahi', 'button_url' => '/shop'],
                ],
                'banners' => [
                    ['title' => 'Blouse Wanita', 'image' => 'seeders/banners/blouse-banner.jpg', 'link_url' => '/shop?category=baju-wanita'],
                ],
                'products' => [
                    [
                        'name' => 'Blouse Sutra Wanita Kerah Pita',
                        'main_image' => 'seeders/products/blouse-sutra.jpg',
                        'is_new_arrival' => true,
                        'sub_category_id' => $catBajuWanita->id,
                        'varians' => [
                            [
                                'options_data' => [['name' => 'Warna', 'value' => 'Putih'], ['name' => 'Ukuran', 'value' => 'S']],
                                'modal_price' => 176000,
                                'profit_percentage' => 25,
                                'stock' => 10,
                            ],
                            [
                                'options_data' => [['name' => 'Warna', 'value' => 'Hitam'], ['name' => 'Ukuran', 'value' => 'M']],
                                'modal_price' => 180000,
                                'profit_percentage' => 25,
                                'stock' => 12,
                            ],
                        ]
                    ],
                    [
                        'name' => 'Celana Chino Pria Slim Fit',
                        'main_image' => 'seeders/products/celana-chino.jpg',
                        'is_best_seller' => true,
                        'sub_category_id' => $catBajuPria->id,
                        'varians' => [
                            [
                                'options_data' => [['name' => 'Warna', 'value' => 'Krem'], ['name' => 'Ukuran', 'value' => '30']],
                                'modal_price' => 220000,
                                'profit_percentage' => 25,
                                'stock' => 15,
                            ],
                            [
                                'options_data' => [['name' => 'Warna', 'value' => 'Abu-abu'], ['name' => 'Ukuran', 'value' => '32']],
                                'modal_price' => 225000,
                                'profit_percentage' => 25,
                                'stock' => 10,
                            ],
                        ]
                    ],
                    [
                        'name' => 'Tas Selempang Kanvas Unisex',
                        'main_image' => 'seeders/products/tas-kanvas.jpg',
                        'sub_category_id' => $catTas->id,
                        'varians' => [
                            [
                                'options_data' => [['name' => 'Warna', 'value' => 'Hijau Army']],
                                'modal_price' => 100000,
                                'profit_percentage' => 50,
                                'stock' => 20,
                            ],
                            [
                                'options_data' => [['name' => 'Warna', 'value' => 'Biru Dongker']],
                                'modal_price' => 105000,
                                'profit_percentage' => 50,
                                'stock' => 18,
                            ],
                        ]
                    ],
                    [
                        'name' => 'Sneakers Kanvas Putih Klasik',
                        'main_image' => 'seeders/products/sneakers-putih.jpg',
                        'sub_category_id' => $catSepatu->id,
                        'varians' => [
                            [
                                'options_data' => [['name' => 'Ukuran', 'value' => '38']],
                                'modal_price' => 256000,
                                'profit_percentage' => 25,
                                'stock' => 8,
                            ],
                            [
                                'options_data' => [['name' => 'Ukuran', 'value' => '39']],
                                'modal_price' => 256000,
                                'profit_percentage' => 25,
                                'stock' => 12,
                            ],
                            [
                                'options_data' => [['name' => 'Ukuran', 'value' => '40']],
                                'modal_price' => 260000,
                                'profit_percentage' => 25,
                                'stock' => 10,
                            ],
                        ]
                    ],
                    [
                        'name' => 'Scarf Polos Bahan Premium',
                        'main_image' => 'seeders/products/scarf-polos.jpg',
                        'sub_category_id' => $catAksesoris->id,
                        'varians' => [
                            [
                                'options_data' => [['name' => 'Warna', 'value' => 'Maroon']],
                                'modal_price' => 76000,
                                'profit_percentage' => 25,
                                'stock' => 25,
                            ],
                            [
                                'options_data' => [['name' => 'Warna', 'value' => 'Navy']],
                                'modal_price' => 78000,
                                'profit_percentage' => 25,
                                'stock' => 20,
                            ],
                        ]
                    ],
                ],
                'vouchers' => [
                    ['code' => 'CHIC15', 'discount' => 15, 'min_spending' => 250000, 'description' => 'Diskon 15% untuk koleksi terbaru.'],
                ]
            ],
            // =================================================================
            // MITRA 3: KULINER
            // =================================================================
            [
                'user' => ['name' => 'Chef Anton', 'email' => 'chef.anton@example.com', 'password' => 'mitra123'],
                'shop' => ['shop_name' => 'Dapur Lezat Nusantara', 'shop_address' => 'Jl. Malioboro No. 50, Yogyakarta', 'postal_code' => '55213', 'product_categories' => 'kuliner'],
                'subdomain_name' => 'dapurlezat',
                'contact' => [
                    'address_line1' => 'Jl. Malioboro No. 50, Sosromenduran, Gedong Tengen, Kota Yogyakarta, DIY, 55213',
                    'phone' => '087755501234',
                    'email' => 'pesan@dapurlezat.id',
                    'working_hours' => "Senin - Minggu: 08:00 - 22:00",
                    'map_embed_code' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3952.903907578238!2d110.3631983153571!3d-7.79971899437905!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a5788a5e5c5d7%3A0x1d3d3a33a4e9e1c!2sJl.%20Malioboro%2C%20Kota%20Yogyakarta%2C%20Daerah%20Istimewa%20Yogyakarta!5e0!3m2!1sen!2sid!4v1678886500000!5m2!1sen!2sid" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>'
                ],
                'custom_tema' => [
                    'shop_logo' => 'seeders/logos/dapur-lezat-logo.png',
                    'primary_color' => '#E53E3E',
                    'secondary_color' => '#6c757d',
                ],
                'shop_settings' => [
                    ['key' => 'theme_color', 'value' => '#E53E3E'],
                ],
                'heroes' => [
                    ['title' => 'Cita Rasa Asli Indonesia', 'subtitle' => 'Resep Warisan Keluarga', 'image' => 'seeders/heroes/rendang-hero.jpg', 'button_text' => 'Lihat Menu', 'button_url' => '/shop'],
                ],
                'banners' => [
                    ['title' => 'Nasi Kotak Spesial', 'image' => 'seeders/banners/nasi-kotak-banner.jpg', 'link_url' => '/shop?category=makanan-berat'],
                ],
                'products' => [
                    [
                        'name' => 'Nasi Rendang Daging Sapi Komplit',
                        'main_image' => 'seeders/products/nasi-rendang.jpg',
                        'is_best_seller' => true,
                        'sub_category_id' => $catMakananBerat->id,
                        'varians' => [
                            [
                                'options_data' => [['name' => 'Porsi', 'value' => 'Reguler']],
                                'modal_price' => 28000,
                                'profit_percentage' => 25,
                                'stock' => 20,
                            ],
                            [
                                'options_data' => [['name' => 'Porsi', 'value' => 'Jumbo']],
                                'modal_price' => 35000,
                                'profit_percentage' => 25,
                                'stock' => 10,
                            ],
                        ]
                    ],
                    [
                        'name' => 'Es Cendol Durian Medan',
                        'main_image' => 'seeders/products/es-cendol.jpg',
                        'sub_category_id' => $catMinuman->id,
                        'varians' => [
                            [
                                'options_data' => [['name' => 'Ukuran', 'value' => 'Kecil']],
                                'modal_price' => 12000,
                                'profit_percentage' => 50,
                                'stock' => 30,
                            ],
                            [
                                'options_data' => [['name' => 'Ukuran', 'value' => 'Besar']],
                                'modal_price' => 15000,
                                'profit_percentage' => 50,
                                'stock' => 20,
                            ],
                        ]
                    ],
                    [
                        'name' => 'Risoles Ragout Ayam (Isi 5)',
                        'main_image' => 'seeders/products/risoles.jpg',
                        'is_new_arrival' => true,
                        'sub_category_id' => $catCamilan->id,
                        'varians' => [
                            [
                                'options_data' => [['name' => 'Paket', 'value' => 'Isi 5']],
                                'modal_price' => 20000,
                                'profit_percentage' => 25,
                                'stock' => 25,
                            ],
                            [
                                'options_data' => [['name' => 'Paket', 'value' => 'Isi 10']],
                                'modal_price' => 35000,
                                'profit_percentage' => 25,
                                'stock' => 10,
                            ],
                        ]
                    ],
                    [
                        'name' => 'Bolu Kukus Gula Merah',
                        'main_image' => 'seeders/products/bolu-kukus.jpg',
                        'sub_category_id' => $catKue->id,
                        'varians' => [
                            [
                                'options_data' => [['name' => 'Ukuran', 'value' => 'Mini']],
                                'modal_price' => 36000,
                                'profit_percentage' => 25,
                                'stock' => 15,
                            ],
                            [
                                'options_data' => [['name' => 'Ukuran', 'value' => 'Standar']],
                                'modal_price' => 50000,
                                'profit_percentage' => 25,
                                'stock' => 8,
                            ],
                        ]
                    ],
                    [
                        'name' => 'Sambal Bawang Botol Premium',
                        'main_image' => 'seeders/products/sambal-bawang.jpg',
                        'is_hot_sale' => true,
                        'sub_category_id' => $catSambal->id,
                        'varians' => [
                            [
                                'options_data' => [['name' => 'Berat', 'value' => '100gr']],
                                'modal_price' => 20000,
                                'profit_percentage' => 50,
                                'stock' => 40,
                            ],
                            [
                                'options_data' => [['name' => 'Berat', 'value' => '250gr']],
                                'modal_price' => 35000,
                                'profit_percentage' => 50,
                                'stock' => 20,
                            ],
                        ]
                    ],
                ],
                'vouchers' => [
                    ['code' => 'MAKANENAK10', 'discount' => 10, 'min_spending' => 100000, 'description' => 'Diskon 10% untuk semua menu makanan.'],
                ]
            ],
        ];

        // 4. Loop dan buat data untuk setiap mitra
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
        $this->command->info("Mitra {$mitra->email} dibuat.");

        // 2. Buat Subdomain
        $subdomain = Subdomain::create([
            'user_id' => $mitra->id,
            'shop_id' => null, // Dikosongkan sementara
            'subdomain_name' => $data['subdomain_name'],
            'status' => 'active'
        ]);
        $this->command->info("Subdomain {$subdomain->subdomain_name} dibuat (sementara tanpa shop_id).");

        // 3. Buat Toko (Shop)
        $shop = Shop::create(array_merge($data['shop'], [
            'user_id' => $mitra->id,
            'subdomain_id' => $subdomain->id,
            'shop_photo' => 'seeders/defaults/shop_photo.jpg',
            'ktp' => 'seeders/defaults/ktp.jpg',
        ]));
        $this->command->info("Toko {$shop->shop_name} dibuat.");

        $subdomain->shop_id = $shop->id;
        $subdomain->save();
        $this->command->info("Subdomain {$subdomain->subdomain_name} diupdate dengan Shop ID: {$shop->id}.");


        // 4. Buat Tenant
        Tenant::create([
            'user_id' => $mitra->id,
            'template_id' => $template->id,
            'subdomain_id' => $subdomain->id,
        ]);
        $this->command->info("Tenant untuk {$mitra->email} dibuat.");

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
            'subdomain_id' => $subdomain->id,
            'total_price' => $userPackage->price_paid,
            'status' => 'completed',
            'order_date' => now(),
            'shipping_address' => 'N/A',
            'shipping_city' => 'N/A',
            'shipping_zip_code' => 'N/A',
            'shipping_phone' => 'N/A',
        ]);
        Payment::create([
            'user_id' => $mitra->id,
            'order_id' => $order->id,
            'subs_package_id' => $package->id,
            'midtrans_order_id' => 'SUB-' . Str::uuid(),
            'midtrans_transaction_status' => 'settlement',
            'midtrans_payment_type' => 'bank_transfer',
            'total_payment' => $userPackage->price_paid
        ]);
        $this->command->info("Paket langganan untuk {$mitra->email} dibuat.");


        // 6. Buat Data Kontak Toko
        Contact::create(array_merge($data['contact'], ['user_id' => $mitra->id]));
        $this->command->info("Kontak untuk {$mitra->email} dibuat.");

        // 7. Buat Custom Tema dan Shop Settings
        CustomTema::create(array_merge($data['custom_tema'], [
            'user_id' => $mitra->id,
            'subdomain_id' => $subdomain->id,
            'shop_name' => $shop->shop_name,
            'shop_logo' => $data['custom_tema']['shop_logo'],
            'primary_color' => $data['custom_tema']['primary_color'],
            'secondary_color' => $data['custom_tema']['secondary_color'],
        ]));
        $this->command->info("Tema kustom untuk {$mitra->email} dibuat.");

        foreach ($data['shop_settings'] as $setting) {
            ShopSetting::create([
                'shop_id' => $shop->id,
                'key' => $setting['key'],
                'value' => $setting['value'],
            ]);
        }
        $this->command->info("Pengaturan toko untuk {$mitra->email} dibuat.");

        // 8. Buat Hero Sliders
        foreach ($data['heroes'] as $index => $hero) {
            Hero::create(array_merge($hero, [
                'shop_id' => $shop->id,
                'order' => $index + 1,
                'is_active' => true,
                'image' => $hero['image']
            ]));
        }
        $this->command->info("Hero sliders untuk {$mitra->email} dibuat.");

        // 9. Buat Banner Promosi
        foreach ($data['banners'] as $index => $banner) {
            Banner::create(array_merge($banner, [
                'shop_id' => $shop->id,
                'order' => $index + 1,
                'is_active' => true,
                'image' => $banner['image']
            ]));
        }
        $this->command->info("Banners untuk {$mitra->email} dibuat.");

        // 10. Buat Produk dan Varian-Varian Fleksibelnya
        foreach ($data['products'] as $productData) {
            // Hapus 'varians' dari $productData sebelum membuat Produk utama
            $variansToCreate = $productData['varians'];
            unset($productData['varians']);

            // Buat Produk Utama
            $product = Product::create([
                'user_id' => $mitra->id,
                'shop_id' => $shop->id,
                'name' => $productData['name'],
                'slug' => Str::slug($productData['name']) . '-' . uniqid(),
                'short_description' => $productData['short_description'] ?? 'Deskripsi singkat yang menarik untuk ' . $productData['name'],
                'description' => $productData['description'] ?? 'Deskripsi lengkap dan detail mengenai ' . $productData['name'] . ', menjelaskan bahan, ukuran, dan keunggulannya.',
                // HAPUS 'modal_price' dan 'profit_percentage' dari sini
                // 'modal_price' => $productData['modal_price'],
                // 'profit_percentage' => $productData['profit_percentage'],
                'sub_category_id' => $productData['sub_category_id'],
                'main_image' => $productData['main_image'],
                'is_best_seller' => $productData['is_best_seller'] ?? false,
                'is_new_arrival' => $productData['is_new_arrival'] ?? false,
                'is_hot_sale' => $productData['is_hot_sale'] ?? false,
                'status' => 'active',
                'sku' => $productData['sku'] ?? null, // Tambahkan SKU jika ada di data produk
            ]);

            // Buat Varian-Varian untuk Produk ini
            foreach ($variansToCreate as $varianData) {
                $sellingPrice = $varianData['modal_price'] * (1 + ($varianData['profit_percentage'] / 100));

                $product->varians()->create([
                    'name' => collect($varianData['options_data'])->pluck('value')->implode(' / '), // Nama varian gabungan
                    'modal_price' => $varianData['modal_price'],
                    'profit_percentage' => $varianData['profit_percentage'],
                    'price' => $sellingPrice, // Simpan harga jual di kolom 'price'
                    'stock' => $varianData['stock'],
                    'options_data' => $varianData['options_data'], // Simpan array opsi ke kolom JSON
                    // 'size' => null, // Set null karena sekarang pakai options_data
                    // 'color' => null, // Set null karena sekarang pakai options_data
                    'description' => null, // Default
                    'status' => 'active', // Default
                    'image_path' => $varianData['image_path'] ?? null, // Jika ada gambar per varian
                ]);
            }
            $this->command->info("Produk '{$product->name}' dan varian-variannya untuk {$mitra->email} dibuat.");
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
        $this->command->info("Voucher untuk {$mitra->email} dibuat.");
    }
}