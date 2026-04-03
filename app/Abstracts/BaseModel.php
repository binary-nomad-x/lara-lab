<?php

namespace App\Abstracts;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

abstract class BaseModel extends Model {

    // HasUuids: Makes primary key uuid
    // BelongsToTenant: handles Global Scope aur Auto-fill tenant_id
    use HasUuids, BelongsToTenant;

    /**
     * Disable auto-incrementing since we use UUIDs.
     */
    public $incrementing = false;

    /**
     * The "type" of the primary key ID.
     */
    protected $keyType = 'string';

    /**
     * Prevent manual ID assignment from request.
     */
    protected $guarded = ['id'];

    /**
     * Override HasUuids method to use Ordered UUIDs (v7 style).
     * This is significantly faster for database indexing than standard UUIDs.
     */
    public function newUniqueId(): string {
        return (string)Str::orderedUuid();
    }
}
