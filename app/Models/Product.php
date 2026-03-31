<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends BaseModel {
    use HasFactory, HasUuids;

    public function variants(): HasMany {
        return $this->hasMany(Variant::class, 'product_id', 'id');
    }

    public function tenant(): BelongsTo {
        return $this->belongsTo(Tenant::class);
    }

}
