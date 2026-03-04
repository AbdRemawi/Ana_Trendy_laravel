<?php

use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CouponController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the application and will be assigned to the "api"
| middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
    // Brands API
    Route::apiResource('brands', BrandController::class)->only('index', 'show');
    Route::get('brands/{slug}', [BrandController::class, 'show']);

    // Products API
    Route::get('products/{id_or_slug}', [ProductController::class, 'showByIdOrSlug'])->where('id_or_slug', '[a-zA-Z0-9-]+');
    Route::apiResource('products', ProductController::class)->only('index');

    // Orders API
    Route::prefix('orders')->group(function () {
        Route::post('/', [OrderController::class, 'store']);
        Route::get('/', [OrderController::class, 'index']);
        Route::get('/stats', [OrderController::class, 'getStats']);
        Route::get('/{order}', [OrderController::class, 'show']);
        Route::post('/{order}/assign-courier', [OrderController::class, 'assignCourier']);
        Route::patch('/{order}/status', [OrderController::class, 'updateStatus']);
        Route::get('/{order}/transitions', [OrderController::class, 'getAvailableTransitions']);
    });

    // Coupons API
    Route::prefix('coupons')->group(function () {
        Route::post('/validate', [CouponController::class, 'validateCoupon']);
        Route::post('/preview', [CouponController::class, 'calculatePreview']);
    });
});
