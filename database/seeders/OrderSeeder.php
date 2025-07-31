<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Shop;
use App\Models\Product;
use App\Models\Order;
use App\Models\Payment;
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

        // 1. Kosongkan tabel terkait pesanan produk
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        OrderItem::truncate();
        // Hanya hapus order yang memiliki shop_id dan bukan order langganan
        Order::whereNotNull('shop_id')->where('total_price', '>', 0)->delete();
        // Hapus payment yang tidak terkait langganan
        Payment::whereNull('subs_package_id')->delete();
        Testimoni::whereNotNull('product_id')->delete();
        Shipping::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->command->info('Data pesanan produk, item, payment, testimoni, dan pengiriman lama telah dihapus.');

        // 2. Ambil mitra yang benar dari MitraTokoSeeder
        $mitraEmails = ['mitra1@gmail.com', 'mitra2@gmail.com', 'mitra3@gmail.com'];
        $mitras = User::whereIn('email', $mitraEmails)->with(['shop.subdomain'])->get();

        if ($mitras->isEmpty()) {
            $this->command->error('Mitra tidak ditemukan! Pastikan MitraTokoSeeder sudah dijalankan dengan email yang benar.');
            return;
        }

        // 3. Ambil pelanggan
        $customer = User::where('email', 'customer@gmail.com')->first();
        if (!$customer) {
            $this->command->error('Pelanggan (customer@gmail.com) tidak ditemukan! Pastikan InitialDataSeeder sudah dijalankan.');
            return;
        }

        // 4. Pengaturan Seeder
        $deliveryMethods = ['ship', 'pickup'];
        $numOrdersPerShop = 15;

        // 5. Looping untuk setiap mitra untuk membuat pesanan
        foreach ($mitras as $mitra) {
            $shop = $mitra->shop;
            if (!$shop) {
                $this->command->warn("Mitra {$mitra->email} tidak memiliki toko. Melewati...");
                continue;
            }

            $subdomain = $shop->subdomain;
            if (!$subdomain) {
                $this->command->warn("Toko {$shop->shop_name} tidak memiliki subdomain. Melewati...");
                continue;
            }

            $mitraProducts = Product::where('shop_id', $shop->id)->with('varians')->get();

            if ($mitraProducts->isEmpty()) {
                $this->command->warn("Toko {$shop->shop_name} tidak memiliki produk. Melewati...");
                continue;
            }

            $this->command->info("Membuat pesanan untuk toko '{$shop->shop_name}'...");

            for ($i = 0; $i < $numOrdersPerShop; $i++) {
                $orderGroupId = Str::uuid(); // [MODIFIKASI] Buat Order Group ID untuk setiap pesanan
                $deliveryMethod = $deliveryMethods[array_rand($deliveryMethods)];
                $createdAt = Carbon::now()->subDays(rand(0, 45))->subHours(rand(0, 23));
                $shopCode = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $shop->shop_name), 0, 3));

                // Buat Order utama
                $order = Order::create([
                    'order_number' => "{$shopCode}-" . $createdAt->format('ymd') . "-" . rand(1000, 9999),
                    'order_group_id' => $orderGroupId, // [MODIFIKASI] Isi order_group_id
                    'user_id' => $customer->id,
                    'shop_id' => $shop->id,
                    'subdomain_id' => $subdomain->id,
                    'total_price' => 0, // Akan di-update
                    'subtotal' => 0, // Akan di-update
                    'shipping_cost' => 0, // Akan di-update
                    'discount_amount' => 0, // Untuk seeder ini, diskon 0
                    'status' => 'completed',
                    'order_date' => $createdAt,
                    'delivery_method' => $deliveryMethod,
                    'shipping_address' => 'Jl. Pelanggan Setia No. ' . rand(1, 100),
                    'shipping_city' => 'Kota Seeder',
                    'shipping_zip_code' => '54321',
                    'shipping_phone' => $customer->phone,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);

                $orderSubtotal = 0;
                $maxItems = $mitraProducts->count();
                $numItems = rand(1, min(3, $maxItems));
                $productIdsInOrder = [];

                // Buat Order Items
                for ($j = 0; $j < $numItems; $j++) {
                    $availableProducts = $mitraProducts->whereNotIn('id', $productIdsInOrder);
                    if ($availableProducts->isEmpty())
                        break;

                    $product = $availableProducts->random();
                    if ($product->varians->isEmpty())
                        continue;

                    $selectedVarian = $product->varians->random();
                    $quantity = rand(1, 2);
                    $itemPrice = (float) $selectedVarian->price;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'product_variant_id' => $selectedVarian->id,
                        'quantity' => $quantity,
                        'price' => $itemPrice,
                    ]);
                    $orderSubtotal += ($quantity * $itemPrice);
                    $productIdsInOrder[] = $product->id;
                }

                // Buat data Shipping
                $shippingCost = 0;
                if ($deliveryMethod === 'ship') {
                    $shippingCost = rand(10000, 30000);
                    Shipping::create(['order_id' => $order->id, 'delivery_service' => ['JNE', 'J&T', 'SiCepat'][array_rand(['JNE', 'J&T', 'SiCepat'])], 'status' => 'delivered', 'receipt_number' => 'AWB' . Str::random(12), 'shipping_cost' => $shippingCost, 'estimated_delivery' => Carbon::parse($createdAt)->addDays(rand(2, 7))]);
                } else {
                    Shipping::create(['order_id' => $order->id, 'delivery_service' => 'Pickup', 'status' => 'picked_up', 'shipping_cost' => 0]);
                }

                $grandTotal = $orderSubtotal + $shippingCost;

                // Update total harga di order
                $order->update(['subtotal' => $orderSubtotal, 'shipping_cost' => $shippingCost, 'total_price' => $grandTotal]);

                // [MODIFIKASI] Buat data Payment untuk order ini
                Payment::create([
                    'order_group_id' => $orderGroupId,
                    'order_id' => $order->id,
                    'user_id' => $customer->id,
                    'subs_package_id' => null, // Penting: null untuk pesanan produk
                    'midtrans_order_id' => 'PAY-' . $orderGroupId,
                    // 'transaction_id' => 'TRANS-' . Str::uuid(),
                    'midtrans_transaction_status' => 'settlement',
                    'midtrans_payment_type' => 'bank_transfer',
                    'midtrans_response' => 'success',
                    'total_payment' => $grandTotal,
                ]);

                // Buat Testimoni
                if (!empty($productIdsInOrder)) {
                    $testimonialProductId = $productIdsInOrder[array_rand($productIdsInOrder)];
                    Testimoni::create(['user_id' => $customer->id, 'product_id' => $testimonialProductId, 'order_id' => $order->id, 'name' => $customer->name, 'content' => 'Sangat puas dengan produk dan layanan dari toko ' . $shop->shop_name . '!', 'rating' => rand(4, 5), 'status' => 'published', 'shop_id' => $shop->id]);
                }
            }
            $this->command->info("{$numOrdersPerShop} pesanan dummy berhasil dibuat untuk toko '{$shop->shop_name}'.");
        }
        $this->command->info('OrderSeeder selesai.');
    }
}
