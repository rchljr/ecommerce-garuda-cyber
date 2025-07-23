<?php

namespace Database\Seeders;

use App\Models\Shop;
use App\Models\Order; // Required for dummy order
use App\Models\Subdomain;
use App\Models\Testimoni;
use App\Models\Product; // Required for products
use App\Models\User; // Required to get customer user
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; // Required for dates

class TenantProductTestimonialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $this->command->info('Memulai TenantProductTestimonialSeeder...');

        // Truncate only Testimonial table
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Testimoni::truncate();
        // Do NOT truncate Order/OrderItem here, as OrderSeeder handles that and we might need existing orders.
        // If you want to ensure no old dummy orders from THIS seeder remain, you'd need a more specific delete.
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->command->info('Tabel testimonis dikosongkan.');

        // Get the specific customer user (created by MitraTokoSeeder)
        $customerUser = User::where('email', 'customer@gmail.com')->first();
        if (!$customerUser) {
            $this->command->warn('Pelanggan customer@gmail.com tidak ditemukan. Pastikan MitraTokoSeeder sudah dijalankan.');
            return;
        }

        // Define data for each tenant
        $tenantsData = [
            [
                'subdomain_name' => 'gayanusantara',
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
                'subdomain_name' => 'chicstyle',
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
                        'status' => 'published',
                    ],
                ]
            ],
            [
                'subdomain_name' => 'dapurlezat',
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

        foreach ($tenantsData as $tenantInfo) {
            $targetSubdomainName = $tenantInfo['subdomain_name'];
            $testimonialsForShop = $tenantInfo['testimonials'];

            // Find the subdomain and eager load its user and shop
            $subdomain = Subdomain::where('subdomain_name', $targetSubdomainName)
                                    ->with('user.shop') // Eager load user and shop through user
                                    ->first();

            if (!$subdomain || !$subdomain->user || !$subdomain->user->shop) {
                $this->command->warn("Subdomain '{$targetSubdomainName}' atau user/tokonya tidak ditemukan. Seeder dilewati.");
                continue;
            }

            $shop = $subdomain->user->shop; // Get the shop through the user relationship
            
            // Eager load varians for products
            $products = Product::where('shop_id', $shop->id)->with('varians')->get();

            if ($products->isEmpty()) {
                $this->command->warn("Toko '{$shop->shop_name}' tidak memiliki produk. Seeder dilewati.");
                continue;
            }

            // Find an existing completed order by the customer for this shop
            $existingCompletedOrder = Order::where('user_id', $customerUser->id)
                                           ->where('shop_id', $shop->id)
                                           ->where('status', Order::STATUS_COMPLETED)
                                           ->first();

            $orderIdForTestimonial = null;
            if ($existingCompletedOrder) {
                $orderIdForTestimonial = $existingCompletedOrder->id;
            } else {
                // If no completed order exists, create a minimal dummy order for the customer
                // This order will be used as a reference for testimonials
                $dummyOrder = Order::create([
                    'user_id' => $customerUser->id, // Link to the customer user
                    'shop_id' => $shop->id,
                    'total_price' => 100000, // Dummy price
                    'status' => Order::STATUS_COMPLETED, // Mark as completed
                    'order_date' => Carbon::now()->subDays(rand(1, 30)),
                    'delivery_method' => 'delivery', // Default for dummy order
                    'shipping_address' => 'Jl. Dummy No. ' . rand(1, 100) . ', Kota Dummy',
                    'shipping_city' => 'Kota Dummy',
                    'shipping_zip_code' => '12345',
                    'shipping_phone' => '08' . rand(1000000000, 9999999999),
                ]);
                $orderIdForTestimonial = $dummyOrder->id;
                $this->command->info("Created dummy completed order {$dummyOrder->id} for customer in shop {$shop->shop_name}.");
            }
            
            // Insert testimonials
            foreach ($testimonialsForShop as $data) {
                $randomProduct = $products->random(); // Select a random product from the shop

                // Testimoni model fields: name, content, rating, status, product_id, user_id, order_id, shop_id
                Testimoni::create([
                    'name' => $data['name'],
                    'content' => $data['content'],
                    'rating' => $data['rating'],
                    'status' => $data['status'],
                    'product_id' => $randomProduct->id,
                    'user_id' => $customerUser->id, // Link to the customer user
                    'order_id' => $orderIdForTestimonial, // Link to the (dummy) order
                    'shop_id' => $shop->id, // Link to the shop
                ]);
            }

            $this->command->info("Berhasil menambahkan " . count($testimonialsForShop) . " testimoni untuk toko '{$shop->shop_name}'.");
        }
        $this->command->info('TenantProductTestimonialSeeder selesai.');
    }
}