<?php

// use Cone\Root\Http\Controllers\Auth\ConfirmPasswordController;
use Cone\Root\Http\Controllers\Auth\ForgotPasswordController;
use Cone\Root\Http\Controllers\Auth\LoginController;
use Cone\Root\Http\Controllers\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Route;

// Login
Route::get('login', [LoginController::class, 'show'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Reset
Route::get('password/reset', [ForgotPasswordController::class, 'show'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'send'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'show'])->name('password.form');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.reset');

// Confirm
// Route::get('password/confirm', [ConfirmPasswordController::class, 'show'])->name('password.confirm');
// Route::post('password/confirm', [ConfirmPasswordController::class, 'confirm']);
