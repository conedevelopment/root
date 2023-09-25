<?php

use Cone\Root\Http\Controllers\NotificationsController;
use Cone\Root\Http\Controllers\ResourceFieldController;
use Illuminate\Support\Facades\Route;

// Notifications
Route::apiResource('notifications', NotificationsController::class)->except(['store']);

// Resource Fields
Route::any('/{resource}/form/fields/{field}', ResourceFieldController::class)->where('field', '.*');
