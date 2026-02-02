<?php

use App\Http\Controllers\MiscController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');


Route::get('/misc', [MiscController::class, 'index']);
Route::get('/get-weather', [MiscController::class, 'getWeather']);


