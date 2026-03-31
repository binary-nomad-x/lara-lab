<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tenant extends BaseModel {
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'slug',
        'is_active',
    ];

    protected $guarded = ['id'];

    public function users() {
        return $this->hasMany(User::class);
    }

    public function products() {
        return $this->hasMany(Product::class);
    }

    public function domains() {
        return $this->hasMany(Domain::class);
    }

    public function orders() {
        return $this->hasMany(Order::class);
    }
}
