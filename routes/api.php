<?php

use App\Actions\Fortify\CreateNewUser;
use App\Http\Controllers\Authentication\LoginController;
use App\Http\Controllers\Authentication\RegisterNewUserController;
use App\Http\Controllers\AttributeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VerificationCodeController;
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

    Route::prefix('permissions')->group(function () {
        Route::name('permissions.')->group(function () {
            Route::match(['put', 'patch'], 'sync', [PermissionController::class, 'sync'])->name('sync');
        });
    });
    Route::apiResource('permissions', PermissionController::class)->only(['store', 'update', 'destroy']);


    Route::apiResource('products', ProductController::class)->only(['store', 'update', 'destroy']);
    Route::prefix('products')->group(function () {
        Route::name('products.')->group(function () {
            Route::match(['put', 'patch'], '{product}/revoke-verification', [ProductController::class, 'revokeVerification'])->name('revoke_verification');
            Route::match(['put', 'patch'], '{product}/verify', [ProductController::class, 'verify'])->name('verify');
        });
    });

    Route::apiResource('promos', PromoController::class)->only(['store', 'update', 'destroy']);
    
    Route::apiResource('roles', RoleController::class)->only(['store', 'update', 'destroy']);
    Route::prefix('roles')->group(function () {
        Route::name('roles.')->group(function () {
            Route::get('{role}/permissions', [RoleController::class, 'permissions'])->name('permissions.index');
            Route::match(['put', 'patch'], '{role}/grant-permission/{permission}', [RoleController::class, 'grantPermission'])->name('permissions.grant');
            Route::delete('{role}/revoke-permission/{permission}', [RoleController::class, 'revokePermission'])->name('permissions.revoke');
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

    // Route::apiResource('users', UserController::class)->only(['store', 'update', 'destroy']);
    Route::prefix('users')->group(function () {
        Route::name('users.')->group(function () {
            Route::get('{user}/roles', [UserController::class, 'roles'])->name('roles.index');
            Route::match(['put', 'patch'], '{user}/assign-role/{role}', [UserController::class, 'assignRole'])->name('roles.assign');
            Route::delete('{user}/remove-role/{role}', [UserController::class, 'removeRole'])->name('roles.remove');
        });
    });

});

/**
 *  <----- The Public Routes ----->
 *  These routes that does not require authentication
 *  or authorization. This includes mostly GET request
 */

Route::controller(RegisterNewUserController::class)->group(function() {
    Route::post('password-less',  'registerWithOnlyEmail')->name('password-less');
    Route::post('register',  'register')->name('register');
    Route::post('otp',  'loginWithOtp')->name('otp');
});
Route::post('login', [LoginController::class, 'login'])->name('login');

Route::apiResource('attributes', AttributeController::class)->only(['index', 'show']);
Route::apiResource('images', ImageController::class)->only(['index', 'show']);
Route::apiResource('permissions', PermissionController::class)->only(['index', 'show']);
Route::apiResource('products', ProductController::class)->only(['index', 'show']);
Route::apiResource('promos', PromoController::class)->only(['index', 'show']);
Route::apiResource('roles', RoleController::class)->only(['index', 'show']);
Route::apiResource('stores', StoreController::class)->only(['index', 'show']);
