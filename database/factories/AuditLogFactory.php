<?php

namespace Database\Factories;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class AuditLogFactory extends Factory {
    protected $model = AuditLog::class;

    public function definition(): array {
        return [
            'action' => $this->faker->randomElement(['CREATE', 'UPDATE', 'DELETE', 'LOGIN']),
            'table_name' => $this->faker->randomElement(['products', 'orders', 'users', 'variants']),
            'payload_before' => json_encode(['old_value' => $this->faker->word]),
            'payload_after' => json_encode(['new_value' => $this->faker->word]),
            'ip_address' => $this->faker->ipv4(),
            'created_at' => Carbon::now()->subDays(rand(1, 30)),
            'updated_at' => Carbon::now(),
        ];
    }
}
