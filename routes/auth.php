<?php

use Cone\Root\Http\Controllers\Auth\ForgotPasswordController;
use Cone\Root\Http\Controllers\Auth\LoginController;
use Cone\Root\Http\Controllers\Auth\ResetPasswordController;
use Cone\Root\Http\Controllers\Auth\TwoFactorController;
use Illuminate\Support\Facades\Route;

// Login
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Reset
Route::get('/password/reset', [ForgotPasswordController::class, 'show'])->name('password.request');
Route::post('/password/email', [ForgotPasswordController::class, 'send'])->name('password.email');
Route::get('/password/reset/{token}/{email}', [ResetPasswordController::class, 'show'])->name('password.reset');
Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

// Verify
Route::get('/two-factor', [TwoFactorController::class, 'verify'])->name('two-factor.verify');
Route::get('/two-factor/resend', [TwoFactorController::class, 'show'])->name('two-factor.show');
Route::post('/two-factor/resend', [TwoFactorController::class, 'resend'])->name('two-factor.resend');
