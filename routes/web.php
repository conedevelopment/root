<?php

use Cone\Root\Http\Controllers\MediaController;
use Illuminate\Support\Facades\Route;

Route::as('root.')
    ->prefix('root')
    ->middleware(['web', 'auth', 'verified'])
    ->group(static function (): void {
        // Media
        Route::apiResource('media', MediaController::class);
    });
