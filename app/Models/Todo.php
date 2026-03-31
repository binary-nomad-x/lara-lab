<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;

class Todo extends Model {

    protected $casts = [
        'completed' => 'boolean',
        'due_date' => 'datetime',
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function scopeCompleted($query) {
        return $query->where('completed', true);
    }

    public function scopePending($query) {
        return $query->where('completed', false);
    }

    public function scopeByPriority($query, $priority = 'high') {
        return $query->where('priority', $priority);
    }

    public function scopeDueSoon($query, $days = 3) {
        return $query->where('due_date', '<=', now()->addDays($days))
            ->where('due_date', '>=', now());
    }

    public function scopeWithFilters($query, Request $request) {
        if ($request->has('status')) {
            if ($request->status === 'completed') {
                $query->completed();
            } elseif ($request->status === 'pending') {
                $query->pending();
            }
        }

        if ($request->has('priority')) {
            $query->byPriority($request->priority);
        }

        if ($request->has('search')) {
            $query->where(function ($q) use ($request)protected $fillable = [
            ]; {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('due_date_from')) {
            $query->where('due_date', '>=', $request->due_date_from);
        }

        if ($request->has('due_date_to')) {
            $query->where('due_date', '<=', $request->due_date_to);
        }

        return $query;
    }
}
