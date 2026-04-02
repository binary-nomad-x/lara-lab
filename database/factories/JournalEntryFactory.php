<?php

namespace Database\Factories;

use App\Models\JournalEntry;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class JournalEntryFactory extends Factory {
    protected $model = JournalEntry::class;

    public function definition(): array {
        return [
            'tenant_id' => $this->faker->word(),
            'ledger_id' => $this->faker->word(),
            'account_id' => $this->faker->word(),
            'debit' => $this->faker->randomFloat(),
            'credit' => $this->faker->randomFloat(),
            'description' => $this->faker->text(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
