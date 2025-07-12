<?php

namespace Database\Seeders;

use App\Models\Shop;
use App\Models\User;
use App\Models\Order;
use App\Models\Tenant;
use App\Models\Payment;
use App\Models\Category;
use App\Models\Template;
use App\Models\Subdomain;
use App\Models\UserPackage;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\SubscriptionPackage;
use Illuminate\Support\Facades\Hash;

class InitialDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Nonaktifkan foreign key checks untuk truncate
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        Category::truncate();
        DB::table('sub_categories')->truncate();
        // Hapus juga data role dan permission lama agar tidak duplikat
        DB::table('roles')->truncate();
        DB::table('permissions')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('model_has_permissions')->truncate();
        DB::table('role_has_permissions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Panggil seeder role dan paket terlebih dahulu untuk memastikan ketersediaan
        $this->call(RolesAndPermissionsSeeder::class);

        // 1. Buat User
        $admin = User::create([
            'id' => (string) Str::uuid(),
            'name' => 'Admin GCI',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin123'),
            'status' => 'active',
        ]);

        $customer = User::firstOrCreate(
            ['email' => 'customer@gmail.com'],
            [
                'name' => 'Pelanggan Setia',
                'password' => Hash::make('customer123'),
                'phone' => '6281211112222',
                'status' => 'active',
            ]
        );

        // 2. Tetapkan role
        $admin->assignRole('admin');
        $customer->assignRole('customer');


        // 3. Data Kategori dan Sub-kategori
        $categoriesData = [
            'Kuliner' => ['Makanan', 'Minuman', 'Snack', 'Kue & Roti', 'Lainnya'],
            'Pakaian & Aksesoris' => ['Baju Wanita', 'Baju Pria', 'Sepatu', 'Tas', 'Jam Tangan', 'Perhiasan', 'Aksesoris Lainnya'],
            'Elektronik' => ['Rumah Tangga', 'Hiburan & Audio', 'Komputer & Aksesoris', 'Elektronik Lainnya'],
        ];

        // 4. Looping untuk membuat setiap kategori dan sub-kategorinya
        foreach ($categoriesData as $categoryName => $subCategories) {
            $category = Category::create([
                'id' => (string) Str::uuid(),
                'name' => $categoryName,
                'slug' => Str::slug($categoryName),
            ]);

            foreach ($subCategories as $subCategoryName) {
                $category->subcategories()->create([
                    'id' => (string) Str::uuid(),
                    'name' => $subCategoryName,
                    'slug' => Str::slug($subCategoryName),
                ]);
            }
        }
    }
}
