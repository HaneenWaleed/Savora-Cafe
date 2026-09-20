<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BeverageController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\FoodItemController;
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

Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);
Route::apiResource('food-items', FoodItemController::class)->only(['index', 'show']);
Route::apiResource('beverages', BeverageController::class)->only(['index', 'show']);

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::apiResource('categories', CategoryController::class)->except(['index', 'show']);
    Route::apiResource('food-items', FoodItemController::class)->except(['index', 'show']);
    Route::apiResource('beverages', BeverageController::class)->except(['index', 'show']);
});
