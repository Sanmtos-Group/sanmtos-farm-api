<?php

use App\Actions\Fortify\CreateNewUser;
use App\Http\Controllers\Authentication\LoginController;
use App\Http\Controllers\Authentication\LogoutController;
use App\Http\Controllers\Authentication\RegisterNewUserController;
use App\Http\Controllers\AttributeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\PaymentController;
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


// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

//Logout route
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('logout', [LogoutController::class, 'logout'])->name('logout');
});



/**
 *  <----- The Auth Routes ----->
 *  These are routes that require an authenticated authorize users
 *  they include mostly routes for creating and updating a resource
 *  POST, PUT, PATCH, DELETE requests etc.
 */
Route::middleware('auth:sanctum')->group(function () {

    // User Information 
    Route::get('/user-profile', [UserController::class, 'profile']);
    Route::get('/user-addresses', [UserController::class, 'addresses']);

   
    Route::apiResource('attributes', AttributeController::class)->only(['store', 'update', 'destroy']);
    Route::prefix('attributes/{attribute}/')->group(function () {
        Route::name('attributes.')->group(function () {
            Route::controller(AttributeController::class)->group(function(){
                Route::delete('force-delete', 'forceDestroy')->name('forceDestroy');
                Route::patch('restore', 'restore')->name('restore');
            });

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
            Route::controller(ProductController::class)->group(function (){
                Route::match(['put', 'patch'], '{product}/revoke-verification', 'revokeVerification')->name('revoke_verification');
                Route::match(['put', 'patch'], '{product}/verify', 'verify')->name('verify');
                Route::post('{product}/promos', 'promosStore')->name('promos.store');
                Route::post('{product}/coupons', 'couponsStore')->name('coupons.store');
            });
        });
    });

    Route::apiResource('promos', PromoController::class)->only(['store', 'update', 'destroy']);
    Route::prefix('promos')->group(function () {
        Route::name('promos.')->group(function () {
            Route::controller(PromoController::class)->group(function (){
                Route::match(['put', 'patch'], '{promo}/cancel', 'cancel')->name('cancel');
                Route::match(['put', 'patch'], '{promo}/continue', 'continue')->name('continue');
                Route::post('{promo}/products', 'attachProducts')->name('products.attach');
                Route::delete('{promo}/products', 'detachProducts')->name('products.detach');
            });
        });
    });

    Route::apiResource('coupons', CouponController::class)->only(['store', 'update', 'destroy']);
    Route::prefix('coupons')->group(function () {
        Route::name('coupons.')->group(function () {
            Route::controller(CouponController::class)->group(function (){
                Route::match(['put', 'patch'], '{coupon}/cancel', 'cancel')->name('cancel');
                Route::match(['put', 'patch'], '{coupon}/continue', 'continue')->name('continue');
                Route::post('{coupon}/products', 'attachProducts')->name('products.attach');
                Route::delete('{coupon}/products', 'detachProducts')->name('products.detach');
            });
        });
    });

    Route::prefix('checkout')->group(function () {
        Route::name('checkout.')->group(function () {
            Route::controller(CheckoutController::class)->group(function (){
                Route::get('summary', 'summary' )->name('checkout.summary');
                // Route::match(['put', 'patch'], '{coupon}/continue', 'continue')->name('continue');
                // Route::post('{coupon}/products', 'attachProducts')->name('products.attach');
                // Route::delete('{coupon}/products', 'detachProducts')->name('products.detach');
            });
        });
    });

    Route::apiResource('roles', RoleController::class)->only(['store', 'update', 'destroy']);
    Route::prefix('roles/{role}/')->group(function () {
        Route::name('roles.permissions.')->group(function () {
            Route::controller(RoleController::class)->group(function (){
                Route::get('permissions', 'permissions')->name('index');
                Route::match(['put', 'patch'], 'grant-permission/{permission}','grantPermission')->name('grant');
                Route::delete('revoke-permission/{permission}', 'revokePermission')->name('revoke');
            });

        });
    });

    Route::apiResource('categories', CategoryController::class )->only(['store', 'update', 'destroy']);

    Route::apiResource('images', ImageController::class)->only(['store', 'update', 'destroy']);
    Route::prefix('images/{image}/')->group(function () {
        Route::name('images.')->group(function () {
            Route::controller(ImageController::class)->group(function(){
                Route::delete('force-delete', 'forceDestroy')->name('forceDestroy');
                Route::patch('restore', 'restore')->name('restore');
            });
        });

    });

    Route::apiResource('stores', StoreController::class)->only(['store', 'update', 'destroy']);
    Route::prefix('stores/{store}')->group(function () {
        Route::name('stores.')->group(function () {
            Route::controller(StoreController::class)->group(function (){
                Route::post('promos', 'promosStore')->name('promos.store');
            });
        });
    });

    // Route::apiResource('users', UserController::class)->only(['store', 'update', 'destroy']);
    Route::prefix('users/{user}/')->group(function () {
        Route::name('users.roles.')->group(function () {
            Route::controller(UserController::class)->group(function (){
                Route::get('roles', 'roles')->name('index');
                Route::match(['put', 'patch'], 'assign-role/{role}',  'assignRole')->name('assign');
                Route::delete('/remove-role/{role}', 'removeRole')->name('remove');
            });

        });
    });

    Route::apiResource('payments', PaymentController::class );
    Route::controller(PaymentController::class)->group(function () {
       Route::post('payments', 'makePayment')->name('payments');
       Route::get('payment-verify', 'handleGatewayCallback')->name('payment.verify');
       Route::get('payment-transaction', 'getAllTransactions')->name('payment.transaction');
       Route::get('payment-customer-detail', 'getAllCustomersTransacted')->name('payment.customer.details');
    });

});

/**
 *  <----- The Public Routes ----->
 *  These are routes that do not require authentication
 *  or authorization. They include mostly GET request
 */

Route::controller(RegisterNewUserController::class)->group(function() {
    Route::post('password-less',  'registerWithOnlyEmail')->name('password-less');
    Route::post('register',  'register')->name('register');
    Route::post('otp',  'loginWithOtp')->name('otp');
    Route::get('account/verify/{token}', 'verifyAccount')->name('user.verify');
});
Route::post('login', [LoginController::class, 'login'])->name('login');

Route::apiResource('attributes', AttributeController::class)->only(['index', 'show']);
Route::prefix('cart-items/')->group(function () {
    Route::name('cart-items.')->group(function () {
        Route::controller(CartController::class)->group(function() {
            Route::get('',  'items')->name('index');
            Route::post('',  'add')->name('add');
            Route::match(['put', 'patch'], '{item}',  'increment')->name('increment');
            Route::delete('{item}',  'decrement')->name('decrement');
            Route::delete('{item}/remove',  'remove')->name('remove');
            Route::delete('',  'clear')->name('clear');
        });
    });
});
Route::apiResource('carts', CartController::class)->only(['index', 'show']);
Route::apiResource('coupons', CouponController::class)->only(['index', 'show']);
Route::prefix('coupons/{coupon}/')->group(function () {
    Route::name('coupons.')->group(function () {
        Route::controller(CouponController::class)->group(function (){
            Route::get('products', 'productsIndex')->name('products.index');
        });
    });
});
Route::apiResource('categories', CategoryController::class )->only(['index', 'show']);
Route::apiResource('images', ImageController::class)->only(['index', 'show']);
Route::apiResource('permissions', PermissionController::class)->only(['index', 'show']);

Route::apiResource('products', ProductController::class)->only(['index', 'show']);
Route::prefix('products/{product}/')->group(function () {
    Route::name('products.')->group(function () {
        Route::controller(ProductController::class)->group(function (){
            Route::get('promos', 'promosIndex')->name('promos.index');
            Route::get('coupons', 'couponsIndex')->name('coupons.index');
        });
    });
});

Route::apiResource('promos', PromoController::class)->only(['index', 'show']);
Route::prefix('promos/{promo}/')->group(function () {
    Route::name('promos.')->group(function () {
        Route::controller(PromoController::class)->group(function (){
            Route::get('products', 'productsIndex')->name('products.index');
        });
    });
});
Route::apiResource('roles', RoleController::class)->only(['index', 'show']);

Route::apiResource('stores', StoreController::class)->only(['index', 'show']);
Route::prefix('stores/{store}/')->group(function () {
    Route::name('stores.')->group(function () {
        Route::controller(StoreController::class)->group(function(){
            Route::get('products', 'productsIndex')->name('products.index');
            Route::get('promos', 'promosIndex')->name('promos.index');
        });
    });
});
