<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\Finance\FinanceController;
use App\Http\Controllers\Web\Inventory\ProductController;
use App\Http\Controllers\Web\Orders\OrderController;
use Illuminate\Support\Facades\Route;

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
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::post('/products/variant/{id}/adjust', [ProductController::class, 'adjustStock'])->name('products.variant.adjust');
    });

    // Orders
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/list', [OrderController::class, 'index'])->name('list');
        Route::get('/{id}', [OrderController::class, 'show'])->name('show');
        Route::post('/{id}/pay', [OrderController::class, 'processPayment'])->name('pay');
        Route::post('/{id}/refund', [OrderController::class, 'refund'])->name('refund');
    });

    // Finance
    Route::prefix('finance')->name('finance.')->group(function () {
        Route::get('/ledger', [FinanceController::class, 'index'])->name('ledger');
        Route::post('/ledger', [FinanceController::class, 'store'])->name('ledger.store');
    });
});

// Redirect root to dashboard if logged in, otherwise login
Route::get('/', function () {
    return auth()->check() ? redirect('/dashboard') : redirect('/login');
});
