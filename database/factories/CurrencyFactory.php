<?php

namespace Database\Factories;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class CurrencyFactory extends Factory {
    protected $model = Currency::class;

    public function definition(): array {
        return [
            'code' => $this->faker->word(),
            'name' => $this->faker->name(),
            'exchange_rate' => $this->faker->randomFloat(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
