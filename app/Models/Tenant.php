<?php

namespace App\Models;

use App\Abstracts\BaseModel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tenant extends BaseModel {
    use HasFactory, HasUuids;

    public function users(): \Illuminate\Database\Eloquent\Relations\HasMany|User {
        return $this->hasMany(User::class);
    }

    public function products(): \Illuminate\Database\Eloquent\Relations\HasMany|Product {
        return $this->hasMany(Product::class);
    }

    public function domains(): \Illuminate\Database\Eloquent\Relations\HasMany|Domain {
        return $this->hasMany(Domain::class);
    }

    public function orders(): \Illuminate\Database\Eloquent\Relations\HasMany|Order {
        return $this->hasMany(Order::class);
    }
}
