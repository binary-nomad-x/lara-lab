<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends BaseModel {
    use HasFactory, HasUuids;

    public function items() {
        return $this->hasMany(OrderItem::class);
    }

    public function tenant() {
        return $this->belongsTo(Tenant::class);
    }
}
