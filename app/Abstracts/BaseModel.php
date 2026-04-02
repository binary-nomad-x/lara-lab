<?php

namespace App\Abstracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

abstract class BaseModel extends Model {

    public $incrementing = false;
    protected $keyType = 'string';

    // Guarded mein ID rakhna achi baat hai
    protected $guarded = ['id'];

    protected static function booted(): void {
        // Multi-tenancy Global Scope
        static::addGlobalScope('tenant', static function (Builder $builder) {
            if (auth()->check() && auth()->user()->tenant_id) {
                $builder->where('tenant_id', auth()->user()->tenant_id);
            }
        });

        static::creating(static function (Model $model) {
            // 1. UUID v7 / Ordered UUID Fix
            // Agar model 'HasUuids' trait use nahi kar raha, to ye line kaam karegi
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string)Str::orderedUuid();
            }

            // 2. Tenant ID Auto-fill
            // Behtar check: check if column exists in table
            if (auth()->check() && auth()->user()->tenant_id) {
                // Column check handle karne ka behtar tareeka
                $model->tenant_id = $model->tenant_id ?? auth()->user()->tenant_id;
            }
        });
    }
}