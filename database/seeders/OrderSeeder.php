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
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Voucher;
use App\Models\Customer;
use App\Models\Varian;
use App\Models\OrderItem;
use App\Models\Testimoni;
use App\Models\Shipping;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        OrderItem::truncate();
        Order::whereNotNull('shop_id')->delete();
        Testimoni::whereNotNull('product_id')->delete();
        Shipping::truncate(); // PENTING: Hapus juga data Shipping lama
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->command->info('Data pesanan, item pesanan, testimoni, dan pengiriman lama dihapus.');

        $mitraEmails = ['hilmi21ti@mahasiswa.pcr.ac.id', 'rachel21ti@mahasiswa.pcr.ac.id', 'chef.anton@example.com'];
        $mitras = User::whereIn('email', $mitraEmails)->get();
        if ($mitras->isEmpty()) {
            $this->command->warn('Tidak ada mitra ditemukan berdasarkan email yang ditentukan. Pastikan MitraTokoSeeder sudah dijalankan dan emailnya sesuai.');
            return;
        }

        $customer = User::where('email', 'customer@gmail.com')->first();
        if (!$customer) {
            $this->command->warn('Pelanggan customer@gmail.com tidak ditemukan. Pastikan MitraTokoSeeder sudah dijalankan.');
            return;
        }
        $customers = collect([$customer]);

        $targetOrderStatus = 'completed';
        $deliveryMethods = ['shipped', 'ready_for_pickup'];

        $numOrdersPerShop = 15;

        foreach ($mitras as $mitra) {
            $shop = $mitra->shop;
            if (!$shop) {
                $this->command->warn("Mitra {$mitra->email} tidak memiliki toko. Melewati pembuatan pesanan.");
                continue;
            }

            $shopCode = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $shop->shop_name), 0, 3));

            $mitraProducts = Product::where('user_id', $mitra->id)
                ->with('varians')
                ->get();

            if ($mitraProducts->isEmpty()) {
                $this->command->warn("Mitra {$mitra->email} tidak memiliki produk. Melewati pembuatan pesanan.");
                continue;
            }

            $this->command->info("Membuat pesanan untuk toko '{$shop->shop_name}' (Shop ID: {$shop->id})...");

            for ($i = 0; $i < $numOrdersPerShop; $i++) {
                $selectedCustomer = $customers->random();
                $deliveryMethod = $deliveryMethods[array_rand($deliveryMethods)];
                $createdAt = Carbon::now()->subDays(rand(0, 45))->subHours(rand(0, 23))->subMinutes(rand(0, 59));

                $datePart = $createdAt->format('ym'); // Format: 2507 (TahunBulan)
                $uniquePart = rand(10000, 99999);
                $orderNumber = "{$shopCode}-{$datePart}-{$uniquePart}";

                $order = Order::create([
                    'order_number' => $orderNumber,
                    'user_id' => $selectedCustomer->id,
                    'shop_id' => $shop->id,
                    'total_price' => 0, // Akan dihitung nanti
                    'status' => $targetOrderStatus,
                    'order_date' => $createdAt,
                    'delivery_method' => $deliveryMethod,
                    'shipping_address' => 'Jl. Dummy No. ' . rand(1, 100) . ', Kota Dummy',
                    'shipping_city' => 'Kota Dummy',
                    'shipping_zip_code' => '12345',
                    'shipping_phone' => '08' . rand(1000000000, 9999999999),
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);

                $orderTotalPrice = 0;
                $numItems = rand(1, 3);
                $productIdsInOrder = []; // <<== [MODIFIKASI 1] Siapkan array untuk menampung ID produk

                for ($j = 0; $j < $numItems; $j++) {
                    $product = $mitraProducts->random();

                    if ($product->varians->isEmpty()) {
                        $this->command->warn("Produk '{$product->name}' tidak memiliki varian. Melewati item pesanan ini.");
                        continue;
                    }
                    $selectedVarian = $product->varians->random();

                    $quantity = rand(1, min(5, $selectedVarian->stock > 0 ? $selectedVarian->stock : 1));
                    $itemPrice = (float) $selectedVarian->selling_price;
                    if (is_null($itemPrice)) {
                        $this->command->warn("Harga jual (selling_price) varian {$selectedVarian->id} adalah NULL. Menggunakan 0.");
                        $itemPrice = 0.00;
                    }

                    if ($itemPrice == 0.00 && ($selectedVarian->modal_price > 0 || $selectedVarian->profit_percentage > 0)) {
                        $this->command->warn("PERINGATAN KRITIS: Harga item varian {$selectedVarian->name} (ID: {$selectedVarian->id}) adalah 0 padahal Modal Price: {$selectedVarian->modal_price}, Profit: {$selectedVarian->profit_percentage}.");
                    }

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'product_variant_id' => $selectedVarian->id,
                        'quantity' => $quantity,
                        'price' => $itemPrice,
                    ]);
                    $orderTotalPrice += ($quantity * $itemPrice);
                    $productIdsInOrder[] = $product->id; // <<== [MODIFIKASI 2] Simpan ID produk ke dalam array
                }
                $order->update(['total_price' => $orderTotalPrice]);

                // --- Logika Shipping ---
                if ($order->delivery_method === 'delivery') {
                    Shipping::create([
                        'order_id' => $order->id,
                        'delivery_service' => ['JNE', 'J&T', 'SiCepat'][array_rand(['JNE', 'J&T', 'SiCepat'])],
                        'status' => 'delivered',
                        'receipt_number' => Str::random(12),
                        'shipping_cost' => rand(10000, 30000),
                        'estimated_delivery' => Carbon::parse($createdAt)->addDays(rand(2, 7)),
                    ]);
                } elseif ($order->delivery_method === 'pickup') {
                    Shipping::create([
                        'order_id' => $order->id,
                        'delivery_service' => 'Pickup',
                        'status' => 'picked_up',
                        'receipt_number' => null,
                        'shipping_cost' => 0,
                        'estimated_delivery' => null,
                    ]);
                }

                // <<== [MODIFIKASI 3] Membuat testimoni dengan order_id dan product_id
                if (!empty($productIdsInOrder)) { // Pastikan pesanan memiliki item produk
                    // Pilih satu product_id secara acak dari item yang ada di pesanan ini
                    $testimonialProductId = $productIdsInOrder[array_rand($productIdsInOrder)];

                    Testimoni::create([
                        'user_id' => optional($selectedCustomer)->id,
                        'product_id' => $testimonialProductId, // <- DATA BARU
                        'order_id' => $order->id, // <- DATA BARU
                        'name' => optional($selectedCustomer)->name ?? 'Pelanggan Anonim',
                        'content' => 'Sangat puas dengan produk dan layanan dari toko ' . $shop->shop_name . '! Pesanan tiba tepat waktu dan kualitasnya luar biasa.',
                        'rating' => rand(4, 5),
                        'status' => 'published',
                        'shop_id' => $shop->id,
                    ]);
                }
            }
            $this->command->info("{$numOrdersPerShop} pesanan dummy dibuat untuk toko '{$shop->shop_name}'.");
        }
        $this->command->info('OrderSeeder selesai.');
    }
}