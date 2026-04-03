<?php

namespace App\Models;

use App\Abstracts\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends BaseModel {
    use HasFactory;

    public function variants(): HasMany {
        return $this->hasMany(Variant::class, 'product_id', 'id');
    }

    public function tenant(): BelongsTo {
        return $this->belongsTo(Tenant::class);
    }

    public function orderItems(): \Illuminate\Database\Eloquent\Relations\HasManyThrough|Product {
        return $this->hasManyThrough(OrderItem::class, Variant::class);
    }
}
