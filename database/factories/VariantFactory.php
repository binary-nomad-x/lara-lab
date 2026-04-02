<?php

namespace Database\Factories;

use App\Models\Variant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class VariantFactory extends Factory {
    protected $model = Variant::class;

    public function definition(): array {
        $attributes = ['Color' => $this->faker->safeColorName, 'Size' => $this->faker->randomElement(['S', 'M', 'L', 'XL'])];
        return [
            'sku' => $this->faker->unique()->bothify('SKU-####-????'),
            'name' => 'Variant ' . $this->faker->word,
            'attributes' => json_encode($attributes),
            'stock' => $this->faker->numberBetween(0, 500),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'cost' => $this->faker->randomFloat(2, 5, 500),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
