<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Domain extends BaseModel {
    use HasFactory, HasUuids;

    protected $fillable = [
        'tenant_id',
        'domain',
        'is_primary',
    ];

    //
}
