<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Template;

class TemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Tambah beberapa template awal
        Template::create([
            'name' => 'Sleek',
            'slug' => 'pakaian-aksesoris',
            'path' => 'template1',
            'description' => 'Desain yang bersih dan modern, memberikan pengalaman pengguna yang intuitif dan elegan.'
        ]);

        Template::create([
            'name' => 'Vibrant',
            'slug' => 'kuliner',
            'path' => 'template2',
            'description' => 'Tampilan penuh warna dan dinamis yang mampu menarik perhatian dan meningkatkan interaksi pengguna.'
        ]);

        Template::create([
            'name' => 'Refined',
            'slug' => 'elektronik',
            'path' => 'template3',
            'description' => 'Desain profesional dengan sentuhan elegan yang menonjolkan kualitas dan kepercayaan.'
        ]);
    }
}
