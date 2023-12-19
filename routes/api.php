<?php

use Cone\Root\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

// Notifications
Route::apiResource('notifications', NotificationController::class)->except(['store']);
