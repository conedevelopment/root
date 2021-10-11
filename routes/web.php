<?php

use Cone\Root\Http\Controllers\DashboardController;
use Cone\Root\Http\Controllers\MediaController;
use Illuminate\Support\Facades\Route;

// Dashboard
Route::get('/', DashboardController::class)->name('dashboard');

// Media
Route::apiResource('media', MediaController::class);
