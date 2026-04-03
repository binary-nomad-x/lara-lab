<?php

namespace App\Models;

use App\Abstracts\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockMovement extends BaseModel {
    use HasFactory;

    public function variant(): \Illuminate\Database\Eloquent\Relations\BelongsTo {
        return $this->belongsTo(Variant::class);
    }
}
