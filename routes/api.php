<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'success' => true,
        'message' => 'FlawMind API',
        'version' => 'v1',
    ]);
});
