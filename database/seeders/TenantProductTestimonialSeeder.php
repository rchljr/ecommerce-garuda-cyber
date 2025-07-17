<?php

namespace Database\Seeders;

use App\Models\Subdomain;
use App\Models\Testimoni;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TenantProductTestimonialSeeder extends Seeder
{
    /**
     * Run the database seeds for a specific tenant.
     *
     * @return void
     */
    public function run(): void
    {
        // Tentukan subdomain tenant target
        $targetSubdomain = 'toko-busana-kita';

        // 1. Cari tenant berdasarkan subdomain
        // Eager load relasi yang dibutuhkan (user dan products) untuk efisiensi
        $subdomain = Subdomain::with('user.products')->where('subdomain_name', $targetSubdomain)->first();

        // Jika tenant tidak ditemukan, hentikan seeder dengan pesan peringatan
        if (!$subdomain || !$subdomain->user) {
            $this->command->warn("Tenant dengan subdomain '{$targetSubdomain}' tidak ditemukan. Seeder dilewati.");
            return;
        }

        // 2. Ambil semua produk yang dimiliki oleh tenant tersebut
        $products = $subdomain->user->products;

        // Jika tenant tidak memiliki produk, hentikan seeder
        if ($products->isEmpty()) {
            $this->command->warn("Tenant '{$targetSubdomain}' tidak memiliki produk. Seeder dilewati.");
            return;
        }

        // 3. Siapkan data testimoni yang relevan dengan produk busana
        $testimonialsData = [
            [
                'name' => 'Rina Amelia',
                'content' => 'Gamisnya cantik sekali! Bahannya adem dan jahitannya rapi. Ukurannya juga pas di badan. Pengiriman cepat, terima kasih!',
                'rating' => 5,
                'status' => 'published',
            ],
            [
                'name' => 'Dian Puspita',
                'content' => 'Suka banget sama kemeja batiknya. Motifnya modern dan warnanya tidak luntur setelah dicuci. Cocok untuk kerja.',
                'rating' => 5,
                'status' => 'published',
            ],
            [
                'name' => 'Andi Pratama',
                'content' => 'Celana chino-nya nyaman dipakai seharian. Bahannya stretch jadi enak buat gerak. Next order warna lain.',
                'rating' => 4,
                'status' => 'pending', // Contoh testimoni yang butuh approval
            ],
            [
                'name' => 'Sari Hartati',
                'content' => 'Model dressnya unik, tidak pasaran. Dapat banyak pujian pas dipakai ke acara. Recommended seller!',
                'rating' => 5,
                'status' => 'published',
            ],
            [
                'name' => 'Bayu Nugroho',
                'content' => 'Jaketnya keren, sesuai gambar. Bahannya tebal tapi tidak panas. Ukuran L pas untuk tinggi 175cm.',
                'rating' => 5,
                'status' => 'published',
            ],
        ];

        // 4. Masukkan data testimoni ke database, hubungkan dengan produk acak milik tenant
        foreach ($testimonialsData as $data) {
            // Ambil satu produk secara acak dari koleksi produk milik tenant
            $randomProduct = $products->random();

            // Tambahkan product_id ke data testimoni
            $data['product_id'] = $randomProduct->id;
            
            // Jika ada user_id di testimoni, bisa juga diisi
            // $data['user_id'] = ... 

            Testimoni::create($data);
        }

        // Beri pesan sukses di console
        $this->command->info("Berhasil menambahkan " . count($testimonialsData) . " testimoni produk untuk tenant '{$targetSubdomain}'.");
    }
}
