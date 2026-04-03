<?php

namespace App\Models;

use App\Abstracts\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends BaseModel {
    use HasFactory;

    public function order(): \Illuminate\Database\Eloquent\Relations\BelongsTo {
        return $this->belongsTo(Order::class);
    }

    public function variant(): \Illuminate\Database\Eloquent\Relations\BelongsTo {
        return $this->belongsTo(Variant::class);
    }
}
