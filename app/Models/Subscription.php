<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subscription extends BaseModel
{
    use HasFactory, HasUuids;
    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = [];
    //
}
