<?php

namespace Database\Factories;

use App\Models\Shipment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ShipmentFactory extends Factory {
    protected $model = Shipment::class;

    public function definition(): array {
        return [
            'data' => [
                'transaction_id' => Str::lower(Str::random(10))
            ]
        ];
    }
}
