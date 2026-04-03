<?php

namespace App\Models;

use App\Abstracts\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends BaseModel {
    use HasFactory;

    public function items(): Order|\Illuminate\Database\Eloquent\Relations\HasMany {
        return $this->hasMany(OrderItem::class);
    }

    public function tenant(): \Illuminate\Database\Eloquent\Relations\BelongsTo {
        return $this->belongsTo(Tenant::class);
    }
}
