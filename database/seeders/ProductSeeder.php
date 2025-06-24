<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Product;

class ProductSeeder extends Seeder
{

public function run(): void
{
    Product::factory()->count(20)->create(); // atau sebanyak yang kamu mau
}
}