<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Variant extends BaseModel {
    use HasFactory, HasUuids;

    protected $guarded = ['id'];

    public function product(): BelongsTo {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function orderItems() {
        return $this->hasMany(OrderItem::class);
    }

    public function getStockAttribute() {
        return $this->stockMovements()->sum('quantity');
    }

    public function stockMovements() {
        return $this->hasMany(StockMovement::class);
    }
}
