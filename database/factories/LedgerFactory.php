<?php

namespace Database\Factories;

use App\Models\Ledger;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class LedgerFactory extends Factory {
    protected $model = Ledger::class;

    public function definition(): array {
        return [
            'tenant_id' => $this->faker->word(),
            'name' => $this->faker->name(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
