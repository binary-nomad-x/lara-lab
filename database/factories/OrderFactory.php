<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class OrderFactory extends Factory {
    protected $model = Order::class;

    public function definition(): array {
        return [
            'status' => $this->faker->randomElement(['Draft', 'Confirmed', 'Processing', 'PaymentPending', 'Completed', 'Cancelled']),
            'total_amount' => $this->faker->randomFloat(2, 50, 5000),
            'currency_code' => 'USD',
            'notes' => $this->faker->sentence(),
            'created_at' => Carbon::now()->subDays(rand(1, 90)),
            'updated_at' => Carbon::now(),
        ];
    }
}
