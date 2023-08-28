<?php

use App\Actions\Fortify\CreateNewUser;
use App\Http\Controllers\AttributeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StoreController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


/**
 *  <----- The Auth Routes ----->
 *  These are routes that requires an authenticated authorize users
 *  they include most routes for creating and updating a resource
 *  POST, PUT, PATCH
 */
Route::middleware('auth:sanctum')->group(function () {

    Route::apiResource('attributes', AttributeController::class)->only(['store', 'update', 'destroy']);
    Route::prefix('attributes')->group(function () {
        Route::name('attributes.')->group(function () {
            Route::delete('{attribute}/force-delete', [AttributeController::class, 'forceDestroy'])->name('forceDestroy');
            Route::patch('{attribute}/restore', [AttributeController::class, 'restore'])->name('restore');
        });

    });

    Route::apiResource('products', ProductController::class)->only(['store', 'update', 'destroy']);
    Route::prefix('products')->group(function () {
        Route::name('products.')->group(function () {
            Route::match(['put', 'patch'], '{product}/revoke-verification', [ProductController::class, 'revokeVerification'])->name('revoke_verification');
            Route::match(['put', 'patch'], '{product}/verify', [ProductController::class, 'verify'])->name('verify');
        });
    });

    Route::apiResource('categories', CategoryController::class );

    Route::apiResource('images', ImageController::class)->only(['store', 'update', 'destroy']);
    Route::prefix('images')->group(function () {
        Route::name('images.')->group(function () {
            Route::delete('{image}/force-delete', [ImageController::class, 'forceDestroy'])->name('forceDestroy');
            Route::patch('{image}/restore', [ImageController::class, 'restore'])->name('restore');
        });

    });

    Route::apiResource('stores', StoreController::class)->only(['store', 'update', 'destroy']);

});

/**
 *  <----- The Public Routes ----->
 *  These are public routes that does not require authentication
 *  or authorization. This includes most GET
 */

Route::post('register', [CreateNewUser::class, 'create'])->name('register');

Route::apiResource('attributes', AttributeController::class)->only(['index', 'show']);
Route::apiResource('images', ImageController::class)->only(['index', 'show']);
Route::apiResource('products', ProductController::class)->only(['index', 'show']);
Route::apiResource('stores', StoreController::class)->only(['index', 'show']);
