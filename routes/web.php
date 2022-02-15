<?php

use Cone\Root\Http\Controllers\Account\AccountController;
use Cone\Root\Http\Controllers\Account\PasswordController;
use Cone\Root\Http\Controllers\Account\ProfileController;
use Cone\Root\Http\Controllers\DashboardController;
use Cone\Root\Http\Controllers\ResourceController;
use Illuminate\Support\Facades\Route;

// Dashboard
Route::get('/', DashboardController::class)->name('dashboard');

// Account
Route::get('/account', AccountController::class)->name('account');
Route::patch('/account/password', PasswordController::class)->name('account.password');
Route::patch('/account/profile', ProfileController::class)->name('account.profile');

// Resources
Route::get('/{resource}', [ResourceController::class, 'index'])->name('resource.index');
Route::get('/{resource}/create', [ResourceController::class, 'create'])->name('resource.create');
Route::post('/{resource}', [ResourceController::class, 'store'])->name('resource.store');
Route::get('/{resource}/{id}', [ResourceController::class, 'show'])->name('resource.show');
Route::get('/{resource}/{id}/edit', [ResourceController::class, 'edit'])->name('resource.edit');
Route::patch('/{resource}/{id}', [ResourceController::class, 'update'])->name('resource.update');
Route::delete('/{resource}/{id}', [ResourceController::class, 'destroy'])->name('resource.destroy');
