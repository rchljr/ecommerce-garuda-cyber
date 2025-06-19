<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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

        // Panggil seeder role terlebih dahulu untuk memastikan role tersedia
        $this->call(RolesAndPermissionsSeeder::class);

        // 1. Buat User Admin Default
        $admin = User::create([
            'id' => (string) Str::uuid(),
            'name' => 'Admin GCI',
            'email' => 'rachellfazaa@gmail.com',
            'password' => Hash::make('admin123'), // Ganti dengan password yang aman
            'status' => 'active',
        ]);

        // 2. Tetapkan role 'admin' ke user tersebut
        // Pastikan role 'admin' sudah dibuat oleh RolesAndPermissionsSeeder
        $admin->assignRole('admin');

        // 3. Data Kategori dan Sub-kategori
        $categoriesData = [
            'Kuliner' => ['Makanan', 'Minuman', 'Snack', 'Kue & Roti'],
            'Pakaian & Aksesoris' => ['Baju Wanita', 'Baju Pria', 'Sepatu', 'Tas', 'Jam Tangan', 'Perhiasan'],
            'Jasa' => ['Desain Grafis', 'Fotografi', 'Perbaikan Elektronik', 'Konsultasi'],
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
