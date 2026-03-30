<?php

use App\Http\Controllers\Api\WeatherController;
use App\Http\Controllers\Web\HomeController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::get('/misc', [HomeController::class, 'index']);

// misc routes
Route::get('/get-weather', [WeatherController::class, 'getWeather']);



