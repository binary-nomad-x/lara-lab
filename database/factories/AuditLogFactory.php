<?php

namespace Database\Factories;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class AuditLogFactory extends Factory
{
    protected $model = AuditLog::class;

    public function definition(): array
    {
        return [
            'tenant_id' => $this->faker->word(),
            'user_id' => $this->faker->randomNumber(),
            'action' => $this->faker->word(),
            'table_name' => $this->faker->name(),
            'payload_before' => $this->faker->words(),
            'payload_after' => $this->faker->words(),
            'ip_address' => $this->faker->ipv4(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
