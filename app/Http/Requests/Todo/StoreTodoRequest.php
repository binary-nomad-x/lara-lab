<?php

namespace App\Http\Requests\Todo;

use Illuminate\Foundation\Http\FormRequest;

class StoreTodoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'due_date' => 'nullable|date|after:now',
            'priority' => 'required|in:low,medium,high',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Title is required',
            'title.string' => 'Title must be a string',
            'title.max' => 'Title cannot be more than 255 characters',
            'description.string' => 'Description must be a string',
            'description.max' => 'Description cannot be more than 1000 characters',
            'due_date.date' => 'Due date must be a valid date',
            'due_date.after' => 'Due date must be in the future',
            'priority.required' => 'Priority is required',
            'priority.in' => 'Priority must be one of: low, medium, high',
        ];
    }
}