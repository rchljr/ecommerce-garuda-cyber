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
            'name' => 'Sleek',
            'slug' => 'pakaian-aksesoris',
            'path' => 'template1',
            'description' => 'Desain yang bersih dan modern, memberikan pengalaman pengguna yang intuitif dan elegan.',
            'image_preview' => 'templates/preview1.png'
        ]);

        Template::create([
            'name' => 'Vibrant',
            'slug' => 'kuliner',
            'path' => 'template2',
            'description' => 'Tampilan penuh warna dan dinamis yang mampu menarik perhatian dan meningkatkan interaksi pengguna.',
            'image_preview' => 'templates/preview2.png'
        ]);

        Template::create([
            'name' => 'Refined',
            'slug' => 'elektronik',
            'path' => 'template3',
            'description' => 'Desain profesional dengan sentuhan elegan yang menonjolkan kualitas dan kepercayaan.',
            'image_preview' => 'templates/preview3.png'
        ]);
        // Template::create([
        //     'name' => 'Refined1',
        //     'slug' => 'elektronik1',
        //     'path' => 'template3',
        //     'description' => 'Desain profesional dengan sentuhan elegan yang menonjolkan kualitas dan kepercayaan.',
        //     'image_preview' => 'templates/preview1.png'
        // ]);
        // Template::create([
        //     'name' => 'Refined2',
        //     'slug' => 'elektronik2',
        //     'path' => 'template3',
        //     'description' => 'Desain profesional dengan sentuhan elegan yang menonjolkan kualitas dan kepercayaan.',
        //     'image_preview' => 'templates/preview2.png'
        // ]);
    }
}
