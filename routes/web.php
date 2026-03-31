<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Inventory
    Route::prefix('inventory')->name('inventory.')->group(function() {
        Route::get('/products', [\App\Http\Controllers\Web\Inventory\ProductController::class, 'index'])->name('products.index');
        Route::get('/products/create', [\App\Http\Controllers\Web\Inventory\ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [\App\Http\Controllers\Web\Inventory\ProductController::class, 'store'])->name('products.store');
    });

    // Orders
    Route::prefix('orders')->name('orders.')->group(function() {
        Route::get('/list', [\App\Http\Controllers\Web\Orders\OrderController::class, 'index'])->name('list');
        Route::get('/{id}', [\App\Http\Controllers\Web\Orders\OrderController::class, 'show'])->name('show');
        Route::post('/{id}/pay', [\App\Http\Controllers\Web\Orders\OrderController::class, 'processPayment'])->name('pay');
    });

    // Finance
    Route::prefix('finance')->name('finance.')->group(function() {
        Route::get('/ledger', [\App\Http\Controllers\Web\Finance\FinanceController::class, 'index'])->name('ledger');
        Route::post('/ledger', [\App\Http\Controllers\Web\Finance\FinanceController::class, 'store'])->name('ledger.store');
    });
});

// Redirect root to dashboard if logged in, otherwise login
Route::get('/', function () {
    return auth()->check() ? redirect('/dashboard') : redirect('/login');
});
