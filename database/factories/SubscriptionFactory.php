<?php

namespace Database\Factories;

use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class SubscriptionFactory extends Factory {
    protected $model = Subscription::class;

    public function definition(): array {
        return [
            'tenant_id' => $this->faker->word(),
            'plan_name' => $this->faker->name(),
            'trial_ends_at' => Carbon::now(),
            'ends_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
