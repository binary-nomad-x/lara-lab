<?php

namespace App\Models;

use App\Abstracts\BaseModel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ActivityHistory extends BaseModel {
    use HasFactory, HasUuids;

    protected $table = 'activity_history';

}
