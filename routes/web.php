<?php

use Cone\Root\Http\Controllers\DashboardController;
use Cone\Root\Http\Controllers\DownloadController;
use Cone\Root\Http\Controllers\ResourceController;
use Illuminate\Support\Facades\Route;

// Dashboard
Route::get('/', DashboardController::class)->name('dashboard');

// Download
Route::get('/download/{medium:uuid}', DownloadController::class)->name('download');

// Resource
Route::get('/{resource}', [ResourceController::class, 'index'])->name('resource.index');
Route::get('/{resource}/create', [ResourceController::class, 'create'])->name('resource.create');
Route::post('/{resource}', [ResourceController::class, 'store'])->name('resource.store');
Route::get('/{resource}/{resourceModel}', [ResourceController::class, 'show'])->name('resource.show');
Route::get('/{resource}/{resourceModel}/edit', [ResourceController::class, 'edit'])->name('resource.edit');
Route::patch('/{resource}/{resourceModel}', [ResourceController::class, 'update'])->name('resource.update');
Route::delete('/{resource}/{resourceModel}', [ResourceController::class, 'destroy'])->name('resource.delete');
Route::post('/{resource}/{resourceModel}/restore', [ResourceController::class, 'restore'])->name('resource.restore');
