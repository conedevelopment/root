<?php

use Cone\Root\Http\Controllers\DashboardController;
use Cone\Root\Http\Controllers\DownloadController;
use Cone\Root\Http\Controllers\ResourceController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;

// Dashboard
Route::get('/', DashboardController::class)->name('dashboard');

// Download
Route::get('/download/{medium:uuid}', DownloadController::class)->name('download');

// Resource
Route::get('/resources', static function (): RedirectResponse {
    return Redirect::route('root.dashboard');
});
Route::get('/resources/{resource}', [ResourceController::class, 'index'])->name('resource.index');
Route::get('/resources/{resource}/create', [ResourceController::class, 'create'])->name('resource.create');
Route::post('/resources/{resource}', [ResourceController::class, 'store'])->name('resource.store');
Route::get('/resources/{resource}/{resourceModel}', [ResourceController::class, 'show'])->name('resource.show');
Route::get('/resources/{resource}/{resourceModel}/edit', [ResourceController::class, 'edit'])->name('resource.edit');
Route::patch('/resources/{resource}/{resourceModel}', [ResourceController::class, 'update'])->name('resource.update');
Route::delete('/resources/{resource}/{resourceModel}', [ResourceController::class, 'destroy'])->name('resource.delete');
Route::post('/resources/{resource}/{resourceModel}/restore', [ResourceController::class, 'restore'])->name('resource.restore');
