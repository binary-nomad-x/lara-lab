<?php

namespace Database\Factories;

use App\Models\DeviceRegistry;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class DeviceRegistryFactory extends Factory
{
    protected $model = DeviceRegistry::class;

    public function definition(): array
    {
        return [
            'tenant_id' => $this->faker->word(),
            'user_id' => $this->faker->randomNumber(),
            'device_id' => $this->faker->word(),
            'device_name' => $this->faker->name(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
