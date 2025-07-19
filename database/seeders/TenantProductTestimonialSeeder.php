<?php

namespace Database\Seeders;

use App\Models\Shop;
use App\Models\Order;
use App\Models\Subdomain;
use App\Models\Testimoni;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TenantProductTestimonialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Hapus testimoni lama untuk menghindari duplikasi
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Testimoni::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Definisikan data untuk setiap tenant
        $tenantsData = [
            [
                'subdomain' => 'gayanusantara',
                'testimonials' => [
                    [
                        'name' => 'Rina Amelia',
                        'content' => 'Kemeja batiknya luar biasa! Bahannya adem dan motifnya sangat detail. Ukuran L pas sekali di badan saya. Pengiriman juga cepat.',
                        'rating' => 5,
                        'status' => 'published',
                    ],
                    [
                        'name' => 'Budi Santoso',
                        'content' => 'Sepatu kulitnya benar-benar asli dan nyaman. Awalnya agak kaku tapi setelah beberapa kali pakai jadi pas. Kualitasnya sepadan dengan harganya.',
                        'rating' => 5,
                        'status' => 'published',
                    ],
                    [
                        'name' => 'Citra Lestari',
                        'content' => 'Tas rotannya cantik dan unik. Cocok untuk jalan-jalan santai. Ukurannya pas untuk dompet dan ponsel. Terima kasih!',
                        'rating' => 4,
                        'status' => 'published',
                    ],
                ]
            ],
            [
                'subdomain' => 'chicstyle',
                'testimonials' => [
                    [
                        'name' => 'Diana Putri',
                        'content' => 'Blouse sutranya sangat elegan. Warnanya sesuai dengan foto dan bahannya jatuh dengan indah. Suka sekali!',
                        'rating' => 5,
                        'status' => 'published',
                    ],
                    [
                        'name' => 'Kevin Sanjaya',
                        'content' => 'Celana chino-nya pas, bahannya sedikit melar jadi nyaman untuk bergerak. Jahitannya juga sangat rapi. Kualitas butik.',
                        'rating' => 5,
                        'status' => 'published',
                    ],
                    [
                        'name' => 'Anita Wijaya',
                        'content' => 'Pengiriman agak telat sehari, tapi scarf-nya bagus banget. Lembut dan warnanya cantik. Mungkin lain kali bisa lebih cepat.',
                        'rating' => 4,
                        'status' => 'pending', // Contoh testimoni yang butuh approval
                    ],
                ]
            ],
            [
                'subdomain' => 'dapurlezat',
                'testimonials' => [
                    [
                        'name' => 'Agus Setiawan',
                        'content' => 'Rendangnya juara! Dagingnya empuk, bumbunya meresap sempurna. Porsinya juga pas. Pasti akan pesan lagi untuk acara keluarga.',
                        'rating' => 5,
                        'status' => 'published',
                    ],
                    [
                        'name' => 'Lia Karmila',
                        'content' => 'Sambal bawangnya bikin nagih! Pedasnya pas dan wangi bawangnya terasa. Kemasan botolnya juga aman, tidak bocor sama sekali.',
                        'rating' => 5,
                        'status' => 'published',
                    ],
                    [
                        'name' => 'Keluarga Santoso',
                        'content' => 'Pesan bolu kukus untuk arisan, semua pada suka. Lembut dan tidak terlalu manis. Ukurannya besar, puas banget!',
                        'rating' => 5,
                        'status' => 'published',
                    ],
                    [
                        'name' => 'Mahasiswa Kost',
                        'content' => 'Es Cendolnya mantap, isiannya banyak dan duriannya terasa. Harga mahasiswa, rasa bintang lima!',
                        'rating' => 4,
                        'status' => 'published',
                    ],
                ]
            ],
        ];

        $this->command->info('Memulai proses seeding testimoni untuk tenant...');

        // Loop untuk setiap data tenant
        foreach ($tenantsData as $tenantInfo) {
            $targetSubdomain = $tenantInfo['subdomain'];
            $testimonialsData = $tenantInfo['testimonials'];

            // 1. Cari subdomain terlebih dahulu
            $subdomain = Subdomain::where('subdomain_name', $targetSubdomain)->first();

            if (!$subdomain) {
                $this->command->warn("Subdomain '{$targetSubdomain}' tidak ditemukan. Seeder dilewati.");
                continue;
            }

            // 2. Cari toko (shop) menggunakan shop_id dari subdomain, dan muat relasi produknya
            $shop = Shop::with('products')->find($subdomain->shop_id);

            if (!$shop) {
                $this->command->warn("Toko untuk subdomain '{$targetSubdomain}' tidak ditemukan. Seeder dilewati.");
                continue;
            }

            // 3. Ambil semua produk yang dimiliki oleh toko tersebut
            $products = $shop->products;

            if ($products->isEmpty()) {
                $this->command->warn("Toko '{$shop->shop_name}' tidak memiliki produk. Seeder dilewati.");
                continue;
            }

            // 4. Buat satu order tiruan untuk tenant ini sebagai referensi
            $dummyOrder = Order::firstOrCreate(
                ['user_id' => $subdomain->user_id, 'status' => 'completed'],
                ['total_price' => 100000, 'order_date' => now()]
            );

            // 5. Masukkan data testimoni, hubungkan dengan produk acak dan order tiruan
            foreach ($testimonialsData as $data) {
                $randomProduct = $products->random();

                // PERBAIKAN: Menyesuaikan data dengan model Testimoni
                $data['product_id'] = $randomProduct->id;
                $data['user_id'] = $subdomain->user_id; // User ID pemilik toko
                $data['order_id'] = $dummyOrder->id; // ID dari order tiruan

                Testimoni::create($data);
            }

            $this->command->info("Berhasil menambahkan " . count($testimonialsData) . " testimoni untuk tenant '{$targetSubdomain}'.");
        }
    }
}
