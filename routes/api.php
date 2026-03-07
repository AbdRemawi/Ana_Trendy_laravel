<?php

use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\CitiesController;
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

    // Cities API
    Route::get('cities', [CitiesController::class, 'index']);
    Route::get('cities/{id}', [CitiesController::class, 'show']);

    // Checkout API
    Route::post('checkout', [CheckoutController::class, 'store']);
});
