<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;

Route::group(['middleware' => 'api'], function ($router) {

    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::put('change', [AuthController::class, 'change']);
        Route::post('login', [AuthController::class, 'login']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::post('me', [AuthController::class, 'me']);
    });
    
    Route::apiResource('shops', ShopController::class)->except(['show', 'destroy']);
    Route::apiResource('products', ProductController::class);
    Route::apiResource('cart', CartController::class);
    Route::apiResource('trans', TransactionController::class);
    Route::get('searchproduct', [ProductController::class, 'searchProduct']);
    Route::post('picture/{id}', [ProductController::class, 'picture']);
    Route::delete('picture/{id}', [ProductController::class, 'destroyImage']);
});
