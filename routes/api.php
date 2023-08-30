<?php

use Cone\Root\Http\Controllers\NotificationsController;
use Illuminate\Support\Facades\Route;

// Notifications
Route::apiResource('notifications', NotificationsController::class)->except(['store']);
