<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'tenant_id' => $this->faker->word(),
            'status' => $this->faker->word(),
            'total_amount' => $this->faker->randomFloat(),
            'currency_code' => $this->faker->word(),
            'snapshot' => $this->faker->words(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
