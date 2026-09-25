<?php

use App\Http\Controllers\Api\Admin\AdminOrderController;
use App\Http\Controllers\Api\Admin\AdminStatsController;
use App\Http\Controllers\Api\Admin\AdminUserController;
use App\Http\Controllers\Api\AiController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BeverageController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ChatbotController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\FoodItemController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PreferenceController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\RecommendationController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => response()->json([
    'app' => 'Savora Cafe API',
    'status' => 'ok',
]));

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

Route::middleware('auth:sanctum')->group(function () {
    // Profile & Preferences
    Route::get('profile', [ProfileController::class, 'show']);
    Route::put('profile', [ProfileController::class, 'update']);
    Route::put('profile/password', [ProfileController::class, 'updatePassword']);
    Route::get('profile/preferences', [PreferenceController::class, 'show']);
    Route::put('profile/preferences', [PreferenceController::class, 'update']);

    // Favorites
    Route::get('favorites', [FavoriteController::class, 'index']);
    Route::post('favorites', [FavoriteController::class, 'store']);
    Route::delete('favorites/{type}/{id}', [FavoriteController::class, 'destroy'])
        ->whereIn('type', ['food', 'beverage'])
        ->whereNumber('id');

    // Recommendations
    Route::get('recommendations', [RecommendationController::class, 'index']);
    Route::get('food-items/{foodItem}/match', [RecommendationController::class, 'matchForFood']);
    Route::get('beverages/{beverage}/match', [RecommendationController::class, 'matchForBeverage']);

    // Cart
    Route::get('cart', [CartController::class, 'index']);
    Route::delete('cart', [CartController::class, 'clear']);
    Route::post('cart/items', [CartController::class, 'store']);
    Route::patch('cart/items/{cartItem}', [CartController::class, 'update']);
    Route::delete('cart/items/{cartItem}', [CartController::class, 'destroy']);

    // Orders
    Route::get('orders', [OrderController::class, 'index']);
    Route::post('orders', [OrderController::class, 'store']);
    Route::get('orders/{order}', [OrderController::class, 'show']);
    Route::post('orders/{order}/cancel', [OrderController::class, 'cancel']);
    Route::delete('orders/{order}', [OrderController::class, 'destroy']);

    // Chatbot
    Route::post('chatbot/ask', [ChatbotController::class, 'ask'])->middleware('throttle:15,1');

    // AI: search, compare, combo
    Route::post('ai/search', [AiController::class, 'search'])->middleware('throttle:15,1');
    Route::post('ai/compare', [AiController::class, 'compare'])->middleware('throttle:15,1');
    Route::get('ai/combo', [AiController::class, 'combo'])->middleware('throttle:15,1');

    Route::get('recommendations/surprise', [RecommendationController::class, 'surprise']);
    Route::get('recommendations/healthy', [RecommendationController::class, 'healthy']);

});

// ---------- Admin ----------
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::apiResource('categories', CategoryController::class)->except(['index', 'show']);
    Route::apiResource('food-items', FoodItemController::class)->except(['index', 'show']);
    Route::apiResource('beverages', BeverageController::class)->except(['index', 'show']);

    Route::prefix('admin')->group(function () {
        // Orders
        Route::get('orders', [AdminOrderController::class, 'index']);
        Route::get('orders/{order}', [AdminOrderController::class, 'show']);
        Route::patch('orders/{order}/status', [AdminOrderController::class, 'updateStatus']);

        // Users
        Route::apiResource('users', AdminUserController::class);

        // Statistics
        Route::prefix('stats')->group(function () {
            Route::get('overview', [AdminStatsController::class, 'overview']);
            Route::get('top-items', [AdminStatsController::class, 'topItems']);
            Route::get('categories', [AdminStatsController::class, 'categories']);
            Route::get('never-ordered', [AdminStatsController::class, 'neverOrdered']);
            Route::get('low-stock', [AdminStatsController::class, 'lowStock']);
            Route::get('sales', [AdminStatsController::class, 'sales']);
        });

    });
});
