<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\Api\AuthApiController;

Route::prefix('v1/auth')->group(function () {
    // Public routes
    Route::post('register', [AuthApiController::class, 'register'])->name('auth.register');
    Route::post('login', [AuthApiController::class, 'login'])->name('auth.login');

    // Protected routes
    Route::middleware('auth:api')->group(function () {
        Route::get('user', [AuthApiController::class, 'user'])->name('auth.user');
        Route::post('logout', [AuthApiController::class, 'logout'])->name('auth.logout');
        Route::post('logout-all', [AuthApiController::class, 'logoutAll'])->name('auth.logout-all');
        Route::post('refresh', [AuthApiController::class, 'refresh'])->name('auth.refresh');
    });
});
