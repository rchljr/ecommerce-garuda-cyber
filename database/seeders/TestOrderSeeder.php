<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Tenant;

class TestOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Membuat contoh pesanan (test order)...');

        // 1. Cari mitra dan pastikan dia punya subdomain
        $mitra = User::where('email', 'mitra@gmail.com')->first();
        if (!$mitra) {
            $this->command->error('Mitra dengan email mitra@gmail.com tidak ditemukan.');
            return;
        }

        // ## PERBAIKAN DI SINI ##
        // Ambil subdomain dari mitra dan pastikan ada
        $subdomain = $mitra->subdomain;
        if (!$subdomain) {
            $this->command->error('Subdomain untuk mitra ini tidak ditemukan. Pastikan seeder toko sudah berjalan.');
            return;
        }

        // 2. Cari produk milik mitra ini
        $product = null;
        $mitra->tenant->execute(function () use ($mitra, &$product) {
            $product = Product::where('user_id', $mitra->id)->first();
        });

        if (!$product) {
            $this->command->error('Produk tidak ditemukan di database tenant. Pastikan seeder produk sudah berjalan.');
            return;
        }

        // 3. Buat atau cari contoh pelanggan
        $customer = User::firstOrCreate(
            ['email' => 'customer@example.com'],
            ['name' => 'Pelanggan Uji Coba', 'password' => bcrypt('password')]
        );

        // 4. Buat data Pesanan (Order)
        $order = Order::create([
            'user_id' => $customer->id,
            'subdomain_id' => $subdomain->id, // Gunakan variabel subdomain yang sudah divalidasi
            'total_price' => $product->price * 1,
            'status' => 'pending',
            'order_date' => now(),
        ]);

        // 5. Buat data Item Pesanan (OrderItem)
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => $product->price,
        ]);

        $this->command->info("Contoh pesanan dengan ID: {$order->id} berhasil dibuat untuk toko {$mitra->shop->shop_name}.");
    }
}
