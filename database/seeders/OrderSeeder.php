<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; // Untuk mendapatkan mitra (pemilik produk)
use App\Models\Shop; // Untuk mendapatkan toko
use App\Models\Subdomain; // Untuk mendapatkan subdomain (masih diperlukan untuk relasi Shop)
use App\Models\Product;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Testimoni; // Import model Testimoni

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Memulai OrderSeeder...');

        // Hapus data pesanan lama untuk menjaga kebersihan
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        OrderItem::truncate();
        Order::truncate();
        Testimoni::truncate(); // Tambahkan truncate untuk testimoni
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->command->info('Data pesanan, item pesanan, dan testimoni lama dihapus.');

        // Ambil semua mitra berdasarkan email yang sudah didefinisikan di MitraTokoSeeder
        $mitraEmails = ['hilmi21ti@mahasiswa.pcr.ac.id', 'rachel21ti@mahasiswa.pcr.ac.id', 'chef.anton@example.com'];
        $mitras = User::whereIn('email', $mitraEmails)->get();
        if ($mitras->isEmpty()) {
            $this->command->warn('Tidak ada mitra ditemukan berdasarkan email yang ditentukan. Pastikan MitraTokoSeeder sudah dijalankan dan emailnya sesuai.');
            return;
        }

        // Ambil pelanggan spesifik yang dibuat di MitraTokoSeeder
        $customer = User::where('email', 'customer@gmail.com')->first();
        if (!$customer) {
            $this->command->warn('Pelanggan customer@gmail.com tidak ditemukan. Pastikan MitraTokoSeeder sudah dijalankan.');
            return;
        }
        $customers = collect([$customer]);

        $orderStatuses = ['completed', 'pending', 'processing', 'cancelled', 'failed'];
        $numOrdersPerShop = 15;

        foreach ($mitras as $mitra) {
            $shop = $mitra->shop; // Asumsi User memiliki relasi hasOne Shop
            if (!$shop) {
                $this->command->warn("Mitra {$mitra->email} tidak memiliki toko. Melewati pembuatan pesanan.");
                continue;
            }

            // Ambil semua produk yang dimiliki oleh mitra ini
            $mitraProducts = Product::where('user_id', $mitra->id)->get();
            if ($mitraProducts->isEmpty()) {
                $this->command->warn("Mitra {$mitra->email} tidak memiliki produk. Melewati pembuatan pesanan.");
                continue;
            }

            $this->command->info("Membuat pesanan untuk toko '{$shop->shop_name}' (Shop ID: {$shop->id})...");

            for ($i = 0; $i < $numOrdersPerShop; $i++) {
                $selectedCustomer = $customers->random();
                $status = $orderStatuses[array_rand($orderStatuses)];
                $createdAt = Carbon::now()->subDays(rand(0, 45))->subHours(rand(0, 23))->subMinutes(rand(0, 59));

                $order = Order::create([
                    'user_id' => $selectedCustomer->id, // ID pelanggan yang membuat pesanan
                    'shop_id' => $shop->id, // Menggunakan shop_id
                    'total_price' => 0, // Akan dihitung nanti
                    'status' => $status,
                    'order_date' => $createdAt,
                    'shipping_address' => 'Jl. Dummy No. ' . rand(1, 100) . ', Kota Dummy',
                    'shipping_city' => 'Kota Dummy',
                    'shipping_zip_code' => '12345',
                    'shipping_phone' => '08' . rand(1000000000, 9999999999),
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);

                $orderTotalPrice = 0;
                $numItems = rand(1, 3);

                for ($j = 0; $j < $numItems; $j++) {
                    $product = $mitraProducts->random();
                    $quantity = rand(1, 5);
                    $itemPrice = $product->price;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'price' => $itemPrice,
                    ]);
                    $orderTotalPrice += ($quantity * $itemPrice);
                }
                $order->update(['total_price' => $orderTotalPrice]);

                // Jika pesanan selesai, buat testimoni
                if ($order->status === 'completed') {
                    Testimoni::create([
                        'name' => optional($selectedCustomer)->name ?? 'Pelanggan Anonim',
                        'content' => 'Sangat puas dengan produk dan layanan dari toko ' . $shop->shop_name . '! Pesanan tiba tepat waktu dan kualitasnya luar biasa.',
                        'rating' => rand(4, 5), // Rating acak 4 atau 5
                        'status' => 'published',
                        'shop_id' => $shop->id, // Tautkan testimoni ke shop_id
                    ]);
                }
            }
            $this->command->info("{$numOrdersPerShop} pesanan dummy dibuat untuk toko '{$shop->shop_name}'.");
        }
        $this->command->info('OrderSeeder selesai.');
    }
}
