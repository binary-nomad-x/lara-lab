<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\TodoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    // Public routes (no authentication required)
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Protected routes (require authentication via Sanctum)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::get('/export-data', [AuthController::class, 'exportData']); // GDPR-compliant data export
        Route::delete('/delete-account', [AuthController::class, 'deleteAccount']); // Right to be Forgotten
    });
});

Route::middleware('auth:sanctum')->prefix('todos')->group(function () {
    Route::get('/', [TodoController::class, 'index']);
    Route::post('/', [TodoController::class, 'store']);
    Route::get('/statistics', [TodoController::class, 'statistics']);
    Route::get('/{todo}', [TodoController::class, 'show']);
    Route::put('/{todo}', [TodoController::class, 'update']);
    Route::delete('/{todo}', [TodoController::class, 'destroy']);
    Route::patch('/{todo}/toggle', [TodoController::class, 'toggleComplete']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');