<?php

use Cone\Root\Http\Controllers\DashboardController;
use Cone\Root\Support\Facades\Resource;
use Illuminate\Support\Facades\Route;

// Dashboard
Route::get('/', DashboardController::class)->name('dashboard');

// Resources
Resource::routes();
