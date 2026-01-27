<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TodoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'completed' => $this->completed,
            'priority' => $this->priority,
            'due_date' => $this->due_date ? $this->due_date->format('Y-m-d H:i:s') : null,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'is_overdue' => $this->due_date && $this->due_date->isPast() && !$this->completed,
            'is_due_soon' => $this->due_date && $this->due_date->diffInDays(now()) <= 3 && !$this->completed,
        ];
    }
}