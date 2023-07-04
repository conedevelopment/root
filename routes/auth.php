<?php

use Cone\Root\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('login', [LoginController::class, 'show'])->name('login.show');
