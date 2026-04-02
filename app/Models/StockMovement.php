<?php

namespace App\Models;

use App\Abstracts\BaseModel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockMovement extends BaseModel {
    use HasFactory, HasUuids;

    public function variant() {
        return $this->belongsTo(Variant::class);
    }
}
