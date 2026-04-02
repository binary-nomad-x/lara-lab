<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;

class Todo extends BaseModel {

    protected $casts = [
        'completed' => 'boolean',
        'due_date' => 'datetime',
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    #[Scope]
    protected function completed($query) {
        return $query->where('completed', true);
    }

    #[Scope]
    protected function pending($query) {
        return $query->where('completed', false);
    }

    #[Scope]
    protected function byPriority($query, $priority = 'high') {
        return $query->where('priority', $priority);
    }

    #[Scope]
    protected function dueSoon($query, $days = 3) {
        return $query->where('due_date', '<=', now()->addDays($days))
            ->where('due_date', '>=', now());
    }

    #[Scope]
    protected function withFilters($query, Request $request) {
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
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
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
