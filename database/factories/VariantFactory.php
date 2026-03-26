<?php

namespace Database\Factories;

use App\Models\Variant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class VariantFactory extends Factory
{
    protected $model = Variant::class;

    public function definition(): array
    {
        return [
            'tenant_id' => $this->faker->word(),
            'product_id' => $this->faker->word(),
            'name' => $this->faker->name(),
            'sku' => $this->faker->word(),
            'price' => $this->faker->randomFloat(),
            'cost' => $this->faker->randomFloat(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
