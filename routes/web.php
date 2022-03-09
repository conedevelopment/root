<?php

use Cone\Root\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// Dashboard
Route::get('/', DashboardController::class)->name('root.dashboard');
