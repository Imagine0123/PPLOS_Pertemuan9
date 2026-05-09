<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['jwt'])->group(function () {

    Route::get('/profile', [
        AuthController::class,
        'profile'
    ]);

    Route::get('/admin-test', function () {
        return response()->json([
            'message' => 'Admin access granted'
        ]);
    })->middleware('role:admin');
});