<?php

namespace Database\Factories;

use App\Models\SyncQueue;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class SyncQueueFactory extends Factory
{
    protected $model = SyncQueue::class;

    public function definition(): array
    {
        return [
            'tenant_id' => $this->faker->word(),
            'device_id' => $this->faker->word(),
            'entity' => $this->faker->word(),
            'action' => $this->faker->word(),
            'payload' => $this->faker->words(),
            'synced_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
