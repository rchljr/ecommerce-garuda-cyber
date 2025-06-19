<?php

namespace Database\Seeders;

use App\Models\Testimoni;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TestimonialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Kosongkan tabel sebelum diisi untuk menghindari duplikasi
        DB::table('testimoni')->truncate();

        $testimonials = [
            [
                'name' => 'Budi Santoso',
                'content' => 'Layanan yang luar biasa! Toko online saya jadi lebih profesional dan penjualannya meningkat drastis. Sangat direkomendasikan!',
                'rating' => 5,
                'status' => 'published',
            ],
            [
                'name' => 'Citra Lestari',
                'content' => 'Platformnya mudah digunakan, bahkan untuk pemula seperti saya. Fitur-fiturnya sangat membantu dalam mengelola produk dan pesanan.',
                'rating' => 5,
                'status' => 'published',
            ],
            [
                'name' => 'Agus Setiawan',
                'content' => 'Tim support sangat responsif dan membantu setiap kali saya mengalami kendala. Harga paketnya juga sangat terjangkau.',
                'rating' => 4,
                'status' => 'pending',
            ],
            [
                'name' => 'Dewi Anggraini',
                'content' => 'Saya suka dengan desain template yang disediakan. Membuat tampilan toko saya jadi lebih menarik dan modern.',
                'rating' => 5,
                'status' => 'published',
            ],
            [
                'name' => 'Eko Prasetyo',
                'content' => 'Fitur integrasi pembayarannya sangat memudahkan customer saya. Proses checkout jadi lebih cepat dan aman.',
                'rating' => 4,
                'status' => 'pending',
            ],
            [
                'name' => 'Fitria Handayani',
                'content' => 'Berkat platform ini, saya bisa menjangkau lebih banyak pelanggan. Omzet bulanan saya naik lebih dari 50%!',
                'rating' => 5,
                'status' => 'published',
            ],
            [
                'name' => 'Gilang Ramadhan',
                'content' => 'Awalnya ragu, tapi setelah mencoba paket trial, saya langsung yakin untuk berlangganan. Worth it!',
                'rating' => 5,
                'status' => 'pending',
            ],
            [
                'name' => 'Hesti Nuraini',
                'content' => 'Ada beberapa fitur yang menurut saya bisa lebih ditingkatkan, tapi secara keseluruhan sudah sangat bagus dan fungsional.',
                'rating' => 4,
                'status' => 'pending',
            ],
            [
                'name' => 'Indra Wijaya',
                'content' => 'Pilihan paketnya fleksibel, bisa disesuaikan dengan skala bisnis. Sangat cocok untuk UMKM yang ingin go digital.',
                'rating' => 5,
                'status' => 'pending',
            ],
            [
                'name' => 'Joko Susilo',
                'content' => 'Sistem manajemen inventarisnya membantu saya untuk tidak kehabisan stok. Pengelolaan jadi lebih efisien.',
                'rating' => 4,
                'status' => 'published',
            ],
        ];

        // Masukkan data ke dalam database
        foreach ($testimonials as $testimonial) {
            Testimoni::create($testimonial);
        }
    }
}

