<?php

use App\Http\Controllers\Api\Admin\AdminOrderController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BeverageController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\FoodItemController;
use App\Http\Controllers\Api\OrderController;
use Illuminate\Support\Facades\Route;

// ---------- Auth ----------
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:5,1');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
    });
});

// ---------- Menu: تصفح عام ----------
Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);
Route::apiResource('food-items', FoodItemController::class)->only(['index', 'show']);
Route::apiResource('beverages', BeverageController::class)->only(['index', 'show']);

// ---------- Cart & Orders (مستخدم مسجّل) ----------
Route::middleware('auth:sanctum')->group(function () {
    Route::get('cart', [CartController::class, 'index']);
    Route::delete('cart', [CartController::class, 'clear']);
    Route::post('cart/items', [CartController::class, 'store']);
    Route::patch('cart/items/{cartItem}', [CartController::class, 'update']);
    Route::delete('cart/items/{cartItem}', [CartController::class, 'destroy']);

    Route::get('orders', [OrderController::class, 'index']);
    Route::post('orders', [OrderController::class, 'store']);
    Route::get('orders/{order}', [OrderController::class, 'show']);
    Route::post('orders/{order}/cancel', [OrderController::class, 'cancel']);
});

// ---------- Admin ----------
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    // Menu management
    Route::apiResource('categories', CategoryController::class)->except(['index', 'show']);
    Route::apiResource('food-items', FoodItemController::class)->except(['index', 'show']);
    Route::apiResource('beverages', BeverageController::class)->except(['index', 'show']);

    // Orders management
    Route::prefix('admin')->group(function () {
        Route::get('orders', [AdminOrderController::class, 'index']);
        Route::get('orders/{order}', [AdminOrderController::class, 'show']);
        Route::patch('orders/{order}/status', [AdminOrderController::class, 'updateStatus']);
    });
});