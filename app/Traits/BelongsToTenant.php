<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToTenant {

    protected static function bootBelongsToTenant(): void {
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (auth()->check() && auth()->user()->tenant_id) {
                $builder->where('tenant_id', auth()->user()->tenant_id);
            }
        });

        static::creating(function ($model) {
            if (auth()->check() && ($tenantId = auth()->user()->tenant_id)) {
                // hasColumn ki jagah isAttributes(column) ya fillable check zyada fast hai
                if (in_array('tenant_id', $model->getFillable()) || array_key_exists('tenant_id', $model->getAttributes())) {
                    $model->tenant_id ??= $tenantId;
                }
            }
        });
    }
}
