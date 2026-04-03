<?php

namespace App\Models;

use App\Abstracts\BaseModel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Variant extends BaseModel {
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'attributes' => 'json'
    ];

    public function product(): BelongsTo {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function orderItems(): \Illuminate\Database\Eloquent\Relations\HasMany|Variant {
        return $this->hasMany(OrderItem::class);
    }

    protected function stock(): Attribute {
        return Attribute::get(
            fn() => $this->stockMovements()->sum('quantity')
        );
    }

    public function stockMovements(): \Illuminate\Database\Eloquent\Relations\HasMany|Variant {
        return $this->hasMany(StockMovement::class);
    }
}
