<?php

namespace Database\Factories;

use App\Models\StockMovement;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class StockMovementFactory extends Factory
{
    protected $model = StockMovement::class;

    public function definition(): array
    {
        return [
            'tenant_id' => $this->faker->word(),
            'variant_id' => $this->faker->word(),
            'quantity' => $this->faker->randomNumber(),
            'type' => $this->faker->word(),
            'reference_id' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
