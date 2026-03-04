<?php

use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\DeliveryCourierController;
use App\Http\Controllers\Admin\DeliveryCourierFeeController;
use App\Http\Controllers\Admin\InventoryTransactionController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LanguageController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Language switcher
Route::get('/language/{locale}', [LanguageController::class, 'switch'])->name('language.switch');

// Guest routes (redirect if authenticated)
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.process');
});

// Logout route (must be authenticated)
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Redirect root to login if not authenticated
Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
})->name('home');

// Protected routes (require authentication)
Route::middleware(['auth'])->group(function () {
    // Dashboard requires 'view dashboard' permission
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('permission:view dashboard')
        ->name('dashboard');
});

// Admin routes - require authentication, rate limiting, and specific permissions
// Rate limiting: 60 requests per minute per user to prevent abuse
Route::middleware(['auth', 'throttle:60,1'])->prefix('admin')->name('admin.')->group(function () {
    // Debug route to check authentication and permissions
    // ONLY AVAILABLE IN LOCAL/DEVELOPMENT ENVIRONMENT
    Route::get('debug-auth', function () {
        if (!app()->environment('local', 'testing')) {
            abort(404);
        }

        return response()->json([
            'authenticated' => auth()->check(),
            'user_id' => auth()->id(),
            'user_name' => auth()->user()?->name,
            'user_email' => auth()->user()?->email,
            'roles' => auth()->user()?->roles->pluck('name')->toArray() ?? [],
            'permissions' => auth()->user()?->getAllPermissions()->pluck('name')->toArray() ?? [],
            'can_manage_brands' => auth()->user()?->hasPermissionTo('manage brands') ?? false,
        ]);
    })->name('debug-auth');

    // Role management routes - require 'manage roles' permission
    Route::middleware(['permission:manage roles'])->group(function () {
        Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('roles', [RoleController::class, 'store'])->name('roles.store');
        Route::get('roles/{role}', [RoleController::class, 'show'])->name('roles.show');
        Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
        Route::get('api/roles/{role}', [RoleController::class, 'get'])->name('roles.get');
    });

    // Permission management routes - require 'manage permissions' permission
    Route::middleware(['permission:manage permissions'])->group(function () {
        Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index');
        Route::get('permissions/create', [PermissionController::class, 'create'])->name('permissions.create');
        Route::post('permissions', [PermissionController::class, 'store'])->name('permissions.store');
        Route::get('permissions/{permission}', [PermissionController::class, 'show'])->name('permissions.show');
        Route::get('permissions/{permission}/edit', [PermissionController::class, 'edit'])->name('permissions.edit');
        Route::put('permissions/{permission}', [PermissionController::class, 'update'])->name('permissions.update');
        Route::delete('permissions/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy');
    });

    // User management routes - require 'manage users' permission
    Route::middleware(['permission:manage users'])->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
        Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // Brand management routes - require 'view brands' or 'manage brands' or 'delete brands' permission
    // Note: Specific routes must come before dynamic routes like {brand}
    Route::middleware(['permission:manage brands'])->group(function () {
        Route::get('brands/create', [BrandController::class, 'create'])->name('brands.create');
        Route::post('brands', [BrandController::class, 'store'])->name('brands.store');
        Route::get('brands/{brand}/edit', [BrandController::class, 'edit'])->name('brands.edit');
        Route::put('brands/{brand}', [BrandController::class, 'update'])->name('brands.update');
    });

    Route::middleware(['permission:view brands'])->group(function () {
        Route::get('brands', [BrandController::class, 'index'])->name('brands.index');
        Route::get('brands/{brand}', [BrandController::class, 'show'])->name('brands.show');
    });

    Route::middleware(['permission:delete brands'])->group(function () {
        Route::delete('brands/{brand}', [BrandController::class, 'destroy'])->name('brands.destroy');
        Route::post('brands/{id}/restore', [BrandController::class, 'restore'])->name('brands.restore');
    });

    // Category management routes - require 'manage categories' permission
    Route::middleware(['permission:manage categories'])->group(function () {
        Route::get('categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
        Route::post('categories/{id}/restore', [CategoryController::class, 'restore'])->name('categories.restore');
        Route::post('categories/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('categories.toggle-status');
    });

    Route::middleware(['permission:view categories'])->group(function () {
        Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
    });

    // Product management routes - require 'manage products' or 'view products' or 'delete products' permission
    // Note: Specific routes must come before dynamic routes like {product}
    Route::middleware(['permission:manage products'])->group(function () {
        Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('products', [ProductController::class, 'store'])->name('products.store');
        Route::get('products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('products/{product}', [ProductController::class, 'update'])->name('products.update');
    });

    Route::middleware(['permission:view products'])->group(function () {
        Route::get('products', [ProductController::class, 'index'])->name('products.index');
        Route::get('products/{product}', [ProductController::class, 'show'])->name('products.show');
    });

    Route::middleware(['permission:delete products'])->group(function () {
        Route::delete('products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
        Route::post('products/{id}/restore', [ProductController::class, 'restore'])->name('products.restore');
    });

    // Inventory Transaction management routes - require 'manage products' or 'view products' permission
    Route::middleware(['permission:manage products'])->group(function () {
        Route::get('inventory/create', [InventoryTransactionController::class, 'create'])->name('inventory.create');
        Route::post('inventory', [InventoryTransactionController::class, 'store'])->name('inventory.store');
        Route::get('inventory/{inventory}/edit', [InventoryTransactionController::class, 'edit'])->name('inventory.edit');
        Route::put('inventory/{inventory}', [InventoryTransactionController::class, 'update'])->name('inventory.update');
        Route::delete('inventory/{inventory}', [InventoryTransactionController::class, 'destroy'])->name('inventory.destroy');
    });

    Route::middleware(['permission:view products'])->group(function () {
        Route::get('inventory', [InventoryTransactionController::class, 'index'])->name('inventory.index');
        Route::get('inventory/{inventory}', [InventoryTransactionController::class, 'show'])->name('inventory.show');
        Route::get('products/{productId}/inventory', [InventoryTransactionController::class, 'byProduct'])->name('inventory.by-product');
    });

    // City management routes - require 'manage cities' permission
    Route::middleware(['permission:manage cities'])->group(function () {
        Route::get('cities/create', [CityController::class, 'create'])->name('cities.create');
        Route::post('cities', [CityController::class, 'store'])->name('cities.store');
        Route::get('cities/{city}/edit', [CityController::class, 'edit'])->name('cities.edit');
        Route::put('cities/{city}', [CityController::class, 'update'])->name('cities.update');
        Route::delete('cities/{city}', [CityController::class, 'destroy'])->name('cities.destroy');
        Route::post('cities/{id}/restore', [CityController::class, 'restore'])->name('cities.restore');
        Route::post('cities/{city}/toggle-status', [CityController::class, 'toggleStatus'])->name('cities.toggle-status');
    });

    Route::middleware(['permission:view cities'])->group(function () {
        Route::get('cities', [CityController::class, 'index'])->name('cities.index');
        Route::get('cities/{city}', [CityController::class, 'show'])->name('cities.show');
    });

    // Delivery Courier management routes - require 'manage delivery couriers' permission
    Route::middleware(['permission:manage delivery couriers'])->group(function () {
        Route::get('delivery-couriers/create', [DeliveryCourierController::class, 'create'])->name('delivery-couriers.create');
        Route::post('delivery-couriers', [DeliveryCourierController::class, 'store'])->name('delivery-couriers.store');
        Route::get('delivery-couriers/{courier}/edit', [DeliveryCourierController::class, 'edit'])->name('delivery-couriers.edit');
        Route::put('delivery-couriers/{courier}', [DeliveryCourierController::class, 'update'])->name('delivery-couriers.update');
        Route::delete('delivery-couriers/{courier}', [DeliveryCourierController::class, 'destroy'])->name('delivery-couriers.destroy');
        Route::post('delivery-couriers/{id}/restore', [DeliveryCourierController::class, 'restore'])->name('delivery-couriers.restore');
        Route::post('delivery-couriers/{courier}/toggle-status', [DeliveryCourierController::class, 'toggleStatus'])->name('delivery-couriers.toggle-status');
    });

    Route::middleware(['permission:view delivery couriers'])->group(function () {
        Route::get('delivery-couriers', [DeliveryCourierController::class, 'index'])->name('delivery-couriers.index');
        Route::get('delivery-couriers/{courier}', [DeliveryCourierController::class, 'show'])->name('delivery-couriers.show');
    });

    // Delivery Courier Fee management routes - require 'manage delivery courier fees' permission
    Route::middleware(['permission:manage delivery courier fees'])->group(function () {
        Route::get('delivery-courier-fees/create', [DeliveryCourierFeeController::class, 'create'])->name('delivery-courier-fees.create');
        Route::post('delivery-courier-fees', [DeliveryCourierFeeController::class, 'store'])->name('delivery-courier-fees.store');
        Route::get('delivery-courier-fees/{fee}/edit', [DeliveryCourierFeeController::class, 'edit'])->name('delivery-courier-fees.edit');
        Route::put('delivery-courier-fees/{fee}', [DeliveryCourierFeeController::class, 'update'])->name('delivery-courier-fees.update');
        Route::delete('delivery-courier-fees/{fee}', [DeliveryCourierFeeController::class, 'destroy'])->name('delivery-courier-fees.destroy');
        Route::post('delivery-courier-fees/{fee}/toggle-status', [DeliveryCourierFeeController::class, 'toggleStatus'])->name('delivery-courier-fees.toggle-status');
    });

    Route::middleware(['permission:view delivery courier fees'])->group(function () {
        Route::get('delivery-courier-fees', [DeliveryCourierFeeController::class, 'index'])->name('delivery-courier-fees.index');
        Route::get('delivery-courier-fees/{fee}', [DeliveryCourierFeeController::class, 'show'])->name('delivery-courier-fees.show');
    });

    // Order management routes - require 'manage orders' or 'view orders' or 'delete orders' permission
    Route::middleware(['permission:manage orders'])->group(function () {
        Route::get('orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
        Route::put('orders/{order}', [OrderController::class, 'update'])->name('orders.update');
        Route::post('orders/{order}/assign-courier', [OrderController::class, 'assignCourier'])->name('orders.assign-courier');
        Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    });

    Route::middleware(['permission:view orders'])->group(function () {
        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    });

    Route::middleware(['permission:delete orders'])->group(function () {
        Route::delete('orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
    });

    // Coupon management routes - require 'manage orders' permission
    Route::middleware(['permission:manage orders'])->group(function () {
        Route::get('coupons/create', [CouponController::class, 'create'])->name('coupons.create');
        Route::post('coupons', [CouponController::class, 'store'])->name('coupons.store');
        Route::get('coupons/{coupon}/edit', [CouponController::class, 'edit'])->name('coupons.edit');
        Route::put('coupons/{coupon}', [CouponController::class, 'update'])->name('coupons.update');
        Route::post('coupons/{coupon}/toggle-status', [CouponController::class, 'toggleStatus'])->name('coupons.toggle-status');
    });

    Route::middleware(['permission:view orders'])->group(function () {
        Route::get('coupons', [CouponController::class, 'index'])->name('coupons.index');
        Route::get('coupons/{coupon}', [CouponController::class, 'show'])->name('coupons.show');
    });

    Route::middleware(['permission:manage orders'])->group(function () {
        Route::delete('coupons/{coupon}', [CouponController::class, 'destroy'])->name('coupons.destroy');
    });
});
