<?php

use Illuminate\Support\Facades\Route;
use Modules\Business\Http\Controllers\Api\BusinessController;
use Modules\Business\Http\Controllers\Api\BusinessLocationController;

/*
|--------------------------------------------------------------------------
| Business Module API Routes
|--------------------------------------------------------------------------
|
| All routes are prefixed with 'api/v1/business' and require
| authentication via Passport bearer token.
|
*/

/*
|--------------------------------------------------------------------------
| Public Routes (no auth required)
|--------------------------------------------------------------------------
*/
Route::prefix('v1/business')->group(function () {
    Route::post('/register', [BusinessController::class, 'register'])->name('business.register');
});

/*
|--------------------------------------------------------------------------
| Protected Routes (auth:api required)
|--------------------------------------------------------------------------
*/
Route::prefix('v1/business')->middleware('auth:api')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Business Settings
    |--------------------------------------------------------------------------
    */
    Route::get('/settings',         [BusinessController::class, 'show'])->name('business.settings.show');
    Route::put('/settings',         [BusinessController::class, 'update'])->name('business.settings.update');
    Route::patch('/settings',       [BusinessController::class, 'update'])->name('business.settings.patch');
    Route::patch('/toggle-active',  [BusinessController::class, 'toggleActive'])->name('business.toggle-active');

    /*
    |--------------------------------------------------------------------------
    | Business Locations
    |--------------------------------------------------------------------------
    */
    Route::prefix('locations')->group(function () {
        Route::get('/',                     [BusinessLocationController::class, 'index'])->name('business.locations.index');
        Route::post('/',                    [BusinessLocationController::class, 'store'])->name('business.locations.store');

        // Named action routes — must be BEFORE the {id} wildcard
        Route::get('/check-location-id',    [BusinessLocationController::class, 'checkLocationId'])->name('business.locations.check-location-id');
        Route::get('/active',               [BusinessLocationController::class, 'activeLocations'])->name('business.locations.active');

        // Wildcard routes
        Route::get('/{id}',                 [BusinessLocationController::class, 'show'])->name('business.locations.show');
        Route::put('/{id}',                 [BusinessLocationController::class, 'update'])->name('business.locations.update');
        Route::patch('/{id}',               [BusinessLocationController::class, 'update'])->name('business.locations.patch');
        Route::delete('/{id}',              [BusinessLocationController::class, 'destroy'])->name('business.locations.destroy');
        Route::patch('/{id}/toggle-active', [BusinessLocationController::class, 'toggleActive'])->name('business.locations.toggle-active');
    });
});
