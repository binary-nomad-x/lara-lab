<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Todo\StoreTodoRequest;
use App\Http\Requests\Todo\UpdateTodoRequest;
use App\Http\Resources\TodoResource;
use App\Models\Todo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $todos = $request->user()->todos()
            ->withFilters($request)
            ->orderBy('priority', 'desc')
            ->orderBy('due_date', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'todos' => TodoResource::collection($todos),
        ]);
    }

    public function store(StoreTodoRequest $request): JsonResponse
    {
        $todo = $request->user()->todos()->create($request->validated());

        return response()->json([
            'message' => 'Todo created successfully',
            'todo' => new TodoResource($todo),
        ], 201);
    }

    public function show(Request $request, Todo $todo): JsonResponse
    {
        if ($todo->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        return response()->json([
            'todo' => new TodoResource($todo),
        ]);
    }

    public function update(UpdateTodoRequest $request, Todo $todo): JsonResponse
    {
        if ($todo->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $todo->update($request->validated());

        return response()->json([
            'message' => 'Todo updated successfully',
            'todo' => new TodoResource($todo),
        ]);
    }

    public function destroy(Request $request, Todo $todo): JsonResponse
    {
        if ($todo->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $todo->delete();

        return response()->json([
            'message' => 'Todo deleted successfully',
        ]);
    }

    public function toggleComplete(Request $request, Todo $todo): JsonResponse
    {
        if ($todo->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $todo->update([
            'completed' => !$todo->completed,
        ]);

        return response()->json([
            'message' => 'Todo status updated successfully',
            'todo' => new TodoResource($todo),
        ]);
    }

    public function statistics(Request $request): JsonResponse
    {
        $user = $request->user();

        $stats = [
            'total' => $user->todos()->count(),
            'completed' => $user->todos()->completed()->count(),
            'pending' => $user->todos()->pending()->count(),
            'high_priority' => $user->todos()->byPriority('high')->count(),
            'overdue' => $user->todos()
                ->where('completed', false)
                ->where('due_date', '<', now())
                ->count(),
            'due_soon' => $user->todos()
                ->pending()
                ->dueSoon(3)
                ->count(),
        ];

        return response()->json([
            'statistics' => $stats,
        ]);
    }
}