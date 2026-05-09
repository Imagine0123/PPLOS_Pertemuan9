<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GatewayController;

// AUTH
Route::prefix('auth')->group(function () {

    Route::post('/register', [
        GatewayController::class,
        'register'
    ]);

    Route::post('/login', [
        GatewayController::class,
        'login'
    ]);

    Route::get('/profile', [
        GatewayController::class,
        'profile'
    ]);
});

// PRODUCTS
Route::get('/products', [
    GatewayController::class,
    'products'
]);

Route::get('/products/{id}', [
    GatewayController::class,
    'product'
]);

Route::post('/products', [
    GatewayController::class,
    'createProduct'
]);

Route::put('/products/{id}', [
    GatewayController::class,
    'updateProduct'
]);

Route::delete('/products/{id}', [
    GatewayController::class,
    'deleteProduct'
]);

// ORDER
Route::get('/orders', [
    GatewayController::class,
    'orders'
]);

Route::post('/orders', [
    GatewayController::class,
    'createOrder'
]);