<?php

namespace Database\Seeders;

use App\Models\Template;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Kosongkan tabel sebelum seeding untuk menghindari duplikat
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Template::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Tambah beberapa template awal
        Template::create([
            'name' => 'Pakaian & Aksesoris',
            'slug' => 'pakaian-aksesoris',
            'path' => 'template1',
            'description' => 'Desain yang bersih dan modern, memberikan pengalaman pengguna yang intuitif dan elegan.',
            'image_preview' => 'preview1.png',
            'status' => 'active'
        ]);

        Template::create([
            'name' => 'Kuliner',
            'slug' => 'kuliner',
            'path' => 'template2',
            'description' => 'Tampilan penuh warna dan dinamis yang mampu menarik perhatian dan meningkatkan interaksi pengguna.',
            'image_preview' => 'preview2.png',
            'status' => 'active'
        ]);

        Template::create([
            'name' => 'Elektronik',
            'slug' => 'elektronik',
            'path' => 'template3',
            'description' => 'Desain profesional dengan sentuhan elegan yang menonjolkan kualitas dan kepercayaan.',
            'image_preview' => 'preview3.png',
            'status' => 'coming_soon'
        ]);
    }
}
