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
        $templates = [
            [
                'name' => 'Fashion Store',
                'slug' => 'template1',
                'path' => 'template1',
                'description' => 'Template bergaya fashion minimalis dan modern.',
            ],
        ];

        foreach ($templates as $template) {
            Template::create($template);
        }
    }
}
