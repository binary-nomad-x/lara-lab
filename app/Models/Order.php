<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends BaseModel
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'tenant_id',
        'status',
        'currency_code',
        'total_amount',
    ];



    public function items() {
        return $this->hasMany(OrderItem::class);
    }
    public function tenant() {
        return $this->belongsTo(Tenant::class);
    }
}
