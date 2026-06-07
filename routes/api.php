<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\User\UserAuthController;
use App\Http\Controllers\Api\User\ProductController as UserProductController;
use App\Http\Controllers\Api\User\CategoryController as UserCategoryController;
use App\Http\Controllers\Api\User\RoutineController;
use App\Http\Controllers\Api\User\ReviewController;
use App\Http\Controllers\Api\User\FavouriteController;
use App\Http\Controllers\Api\User\CartController;
use App\Http\Controllers\Api\User\OrderController as UserOrderController;
use App\Http\Controllers\Api\User\PaymentBankController as UserPaymentBankController;
use App\Http\Controllers\Api\User\SkinTypeController as UserSkinTypeController;
use App\Http\Controllers\Api\User\ProfileController;

use App\Http\Controllers\Api\Admin\AdminAuthController;
use App\Http\Controllers\Api\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Api\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Api\Admin\SkinTypeController as AdminSkinTypeController;
use App\Http\Controllers\Api\Admin\PaymentBankController as AdminPaymentBankController;
use App\Http\Controllers\Api\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Api\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Api\Admin\DashboardController as AdminDashboardController;

Route::prefix('admin')->group(function () {
    Route::middleware('throttle:10,1')->group(function () {
        Route::post('/register', [AdminAuthController::class, 'register']);
        Route::post('/login', [AdminAuthController::class, 'login']);
    });

    Route::middleware(['auth:sanctum', 'admin.auth', 'throttle:60,1'])->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout']);

        Route::get('/profile', [AdminProfileController::class, 'show']);
        Route::put('/profile', [AdminProfileController::class, 'update']);

        Route::get('/dashboard', [AdminDashboardController::class, 'index']);

        Route::get('/categories', [AdminCategoryController::class, 'index']);
        Route::post('/categories', [AdminCategoryController::class, 'store']);
        Route::get('/categories/{category}', [AdminCategoryController::class, 'show']);
        Route::put('/categories/{category}', [AdminCategoryController::class, 'update']);
        Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy']);

        Route::get('/products', [AdminProductController::class, 'index']);
        Route::post('/products', [AdminProductController::class, 'store']);
        Route::get('/products/{product}', [AdminProductController::class, 'show']);
        Route::put('/products/{product}', [AdminProductController::class, 'update']);
        Route::delete('/products/{product}', [AdminProductController::class, 'destroy']);

        Route::get('/skin-types', [AdminSkinTypeController::class, 'index']);
        Route::post('/skin-types', [AdminSkinTypeController::class, 'store']);
        Route::get('/skin-types/{skinType}', [AdminSkinTypeController::class, 'show']);
        Route::put('/skin-types/{skinType}', [AdminSkinTypeController::class, 'update']);
        Route::delete('/skin-types/{skinType}', [AdminSkinTypeController::class, 'destroy']);

        Route::get('/payment-banks', [AdminPaymentBankController::class, 'index']);
        Route::post('/payment-banks', [AdminPaymentBankController::class, 'store']);
        Route::get('/payment-banks/{paymentBank}', [AdminPaymentBankController::class, 'show']);
        Route::put('/payment-banks/{paymentBank}', [AdminPaymentBankController::class, 'update']);
        Route::delete('/payment-banks/{paymentBank}', [AdminPaymentBankController::class, 'destroy']);

        Route::get('/orders', [AdminOrderController::class, 'index']);
        Route::get('/orders/{order}', [AdminOrderController::class, 'show']);
        Route::post('/orders/{order}/approve-payment', [AdminOrderController::class, 'approvePayment']);
        Route::post('/orders/{order}/reject-payment', [AdminOrderController::class, 'rejectPayment']);

        Route::apiResource('admins', \App\Http\Controllers\Api\Admin\AdminController::class);
        Route::apiResource('users', \App\Http\Controllers\Api\Admin\UserController::class);
        Route::apiResource('brands', \App\Http\Controllers\Api\Admin\BrandController::class);
        Route::apiResource('routines', \App\Http\Controllers\Api\Admin\RoutineController::class);
        Route::get('/recommendations', [\App\Http\Controllers\Api\Admin\RecommendationController::class, 'index']);
        Route::get('/recommendations/{recommendation}', [\App\Http\Controllers\Api\Admin\RecommendationController::class, 'show']);
        Route::get('/reviews', [\App\Http\Controllers\Api\Admin\ReviewController::class, 'index']);
        Route::get('/reviews/{review}', [\App\Http\Controllers\Api\Admin\ReviewController::class, 'show']);
        Route::get('/favourites', [\App\Http\Controllers\Api\Admin\FavouriteController::class, 'index']);
        Route::get('/favourites/{favourite}', [\App\Http\Controllers\Api\Admin\FavouriteController::class, 'show']);
        Route::get('/notifications', [\App\Http\Controllers\Api\Admin\NotificationController::class, 'index']);
        Route::get('/notifications/{notification}', [\App\Http\Controllers\Api\Admin\NotificationController::class, 'show']);
    });
});

Route::prefix('user')->group(function () {
    Route::middleware('throttle:10,1')->group(function () {
        Route::post('/register', [UserAuthController::class, 'register']);
        Route::post('/login', [UserAuthController::class, 'login']);
    });

    Route::middleware('throttle:60,1')->group(function () {
        Route::get('/products', [UserProductController::class, 'index']);
        Route::get('/products/{product}', [UserProductController::class, 'show']);
        Route::get('/categories', [UserCategoryController::class, 'index']);
        Route::get('/skin-types', [UserSkinTypeController::class, 'index']);
        Route::get('/payment-banks', [UserPaymentBankController::class, 'index']);
    });

    Route::middleware(['auth:sanctum', 'user.auth', 'throttle:60,1'])->group(function () {
        Route::post('/logout', [UserAuthController::class, 'logout']);

        Route::get('/profile', [ProfileController::class, 'show']);
        Route::put('/profile', [ProfileController::class, 'update']);

        Route::get('/cart', [CartController::class, 'show']);
        Route::post('/cart/items', [CartController::class, 'addItem']);
        Route::put('/cart/items/{cartItem}', [CartController::class, 'updateItem']);
        Route::delete('/cart/items/{cartItem}', [CartController::class, 'removeItem']);

        Route::get('/orders', [UserOrderController::class, 'index']);
        Route::post('/orders/checkout', [UserOrderController::class, 'checkout']);
        Route::get('/orders/{order}', [UserOrderController::class, 'show']);
        Route::post('/orders/{order}/payment', [UserOrderController::class, 'submitPayment']);

        Route::get('/routines', [RoutineController::class, 'index']);
        Route::post('/routines', [RoutineController::class, 'store']);
        Route::get('/routines/{routine}', [RoutineController::class, 'show']);
        Route::put('/routines/{routine}/steps', [RoutineController::class, 'updateSteps']);
        Route::delete('/routines/{routine}', [RoutineController::class, 'destroy']);

        Route::get('/favourites', [FavouriteController::class, 'index']);
        Route::post('/favourites', [FavouriteController::class, 'store']);
        Route::delete('/favourites/{favourite}', [FavouriteController::class, 'destroy']);

        Route::post('/reviews', [ReviewController::class, 'store']);
        Route::delete('/reviews/{review}', [ReviewController::class, 'destroy']);
    });
});
