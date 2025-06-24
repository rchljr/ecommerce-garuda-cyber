<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id ?? User::factory(),     // ambil user yang ada, kalau belum ada buat
            'category_id' => Category::inRandomOrder()->first()->id ?? Category::factory(),
            'name' => $this->faker->words(2, true),
            'description' => $this->faker->sentence(8),
            'price' => $this->faker->numberBetween(10000, 1000000),
            'stock' => $this->faker->numberBetween(1, 100),
            'product_discount' => $this->faker->randomElement([0, 5, 10, 20, 30]),
            'status' => $this->faker->randomElement(['active', 'inactive']),
            'thumbnail' => 'products/default.png', // pastikan file ini ada di storage
        ];
    }
}
