<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'sku' => $this->faker->unique()->bothify('PRD-####-????'),
            'description' => $this->faker->paragraph(),
            'created_at' => Carbon::now()->subDays(rand(1, 365)),
            'updated_at' => Carbon::now(),
        ];
    }
}
