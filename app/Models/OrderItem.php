<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends BaseModel
{
    use HasFactory, HasUuids;


    //

    public function order() {
        return $this->belongsTo(Order::class);
    }
    public function variant() {
        return $this->belongsTo(Variant::class);
    }
}
