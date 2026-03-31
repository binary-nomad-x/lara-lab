<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BaseModel extends Model {

    protected $guarded = ['id'];
    public $incrementing = false;
    protected $keyType = 'string';

    protected static function booted(): void {
        // For CLI or initial migration this might fail if not careful,
        // but let's assume `tenant_id` scoping logic here.
        // For Enterprise SaaS, we can use a session variable or auth()->user()->tenant_id
        static::addGlobalScope('tenant', static function (Builder $builder) {
            if (auth()->check() && auth()->user()->tenant_id) {
                $builder->where('tenant_id', auth()->user()->tenant_id);
            }
        });

        static::creating(static function (Model $model) {
            $model->id = Str::uuid7();
            if (auth()->check() && auth()->user()->tenant_id && in_array('tenant_id', $model->getFillable()) && empty($model->tenant_id)) {
                $model->tenant_id = auth()->user()->tenant_id;
            }
        });
    }
}
