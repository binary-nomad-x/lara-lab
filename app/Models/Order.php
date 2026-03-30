<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends BaseModel
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'tenant_id',
        'status',
        'currency_code',
        'total_amount',
    ];

    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = ['id'];

}
