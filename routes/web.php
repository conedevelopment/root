<?php

use Cone\Root\Http\Controllers\DashboardController;
use Cone\Root\Http\Controllers\NotificationsController;
use Illuminate\Support\Facades\Route;

// Dashboard
Route::get('/', DashboardController::class)->name('dashboard');

// API Routes
Route::prefix('api')->as('api.')->group(static function (): void {
    // Notifications
    Route::apiResource('notifications', NotificationsController::class)->except(['store']);
});
