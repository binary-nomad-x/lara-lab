<?php

namespace Database\Factories;

use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        return [
            'tenant_id' => $this->faker->word(),
            'order_id' => $this->faker->word(),
            'variant_id' => $this->faker->word(),
            'quantity' => $this->faker->randomNumber(),
            'unit_price' => $this->faker->randomFloat(),
            'total' => $this->faker->randomFloat(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
