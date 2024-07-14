<?php

use App\Actions\Fortify\CreateNewUser;
use App\Http\Controllers\Authentication\LoginController;
use App\Http\Controllers\Authentication\LogoutController;
use App\Http\Controllers\Authentication\PasswordResetController;
use App\Http\Controllers\Authentication\RegisterNewUserController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\AttributeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\DiscountTypeController;
use App\Http\Controllers\FeatureController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\LogisticCompanyController;
use App\Http\Controllers\NotificationPreferenceController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentGatewayController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\StoreInvitationController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VerificationCodeController;
use App\Http\Middleware\CheckoutSummarySessionCleaner;

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

Route::controller(PaymentController::class)->group(function () {
    Route::get('payments/callback', 'callback')->name('payments.callback');
    Route::post('payments/webhook', 'webhook')->name('payments.webhook');
    Route::match(['put', 'patch'], 'payments/{payment}/verify', 'verify')->name('payments.verify');
});

/**
 *  <----- The Auth Routes ----->
 *  These are routes that require an authenticated authorize users
 *  they include mostly routes for creating and updating a resource
 *  POST, PUT, PATCH, DELETE requests etc.
 */
Route::middleware('auth:sanctum')->group(function () {

    // Logout route
    Route::post('logout', [LogoutController::class, 'logout'])->name('logout');

    Route::prefix('user')->group(function () {
        Route::name('user.')->group(function () {
            Route::controller(UserController::class)->group(function(){
                Route::get('profile', 'profile')->name('profile.index');
                Route::match(['put', 'patch'],'profile', 'updateProfile')->name('profile.update');
                Route::post('profile-photo', 'updateProfilePhoto')->name('profile-photo.update');

                Route::get('preference', 'indexPreference')->name('preference.index');
                Route::post('preference', 'upsertPreference')->name('preference.upsert');

                Route::get('notification-preferences', 'indexNotificationPreference')->name('notification-preference.index');
                Route::get('notification-preferences/{notificationPreference}', 'showNotificationPreference')->name('notification-preference.show');
                Route::post('notification-preferences', 'subscribeNotificationPreference')->name('notification-preference.subscribe');
                Route::delete('notification-preferences', 'unsubscribeNotificationPreference')->name('notification-preference.subscribe');

                Route::middleware(['merge.user.id.filter'])->group(function () {
                    Route::apiResource('orders', OrderController::class)->except(['destroy']);
                });

                Route::middleware(['merge.user.addressable.filter'])->group(function () {
                    Route::apiResource('addresses', AddressController::class);
                });

            });
        });
    });

    Route::put('change-password', [PasswordResetController::class, 'changePassword'])->name('change-passowrd');


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
                Route::match(['put', 'patch'], '{product}/revoke-verification', 'revokeVerification')->name('revoke-verification');
                Route::match(['put', 'patch'], '{product}/verify', 'verify')->name('verify');
                Route::post('{product}/promos', 'promosStore')->name('promos.store');
                Route::post('{product}/coupons', 'couponsStore')->name('coupons.store');

                /**
                 * <-----  PRODUCT LIKES  ----->
                 *
                */
                    // view product likes
                    Route::get('{product}/likes', [ProductController::class, 'indexLikes'])->name('likes.index');
                    // like a product
                    Route::post('{product}/likes', [ProductController::class, 'createLikes'])->name('likes.create');
                    // undo a product like
                    Route::delete('{product}/likes', [ProductController::class, 'destroyLikes'])->name('likes.destroy');
                    // undo all product likes
                    Route::delete('{product}/likes/all', [ProductController::class, 'destroyAllLikes'])->name('likes.destroy.all');
                /**
                 * <------ END OF PRODUCT LIKES ----->
                 *
                */

                //<------ PRODUCT RATING ------>
                    // view product ratings
                    Route::get('{product}/ratings', [ProductController::class, 'indexRatings'])->name('ratings.index');
                    // rate a product
                    Route::post('{product}/ratings', [ProductController::class, 'createRatings'])->name('ratings.create');
                    // update product rating
                    Route::match(['put','patch'], '{product}/ratings', [ProductController::class, 'updateRatings'])->name('ratings.update');
                     // destroy product rating
                     Route::delete('{product}/ratings', [ProductController::class, 'destroyRatings'])->name('ratings.destroy');
                    // delete all product ratings
                    Route::delete('{product}/ratings/all', [ProductController::class, 'destroyAllRatings'])->name('ratings.destroy.all');
                //<------ END OF PRODUCT RATINGS ----->
            });
        });
    });

    Route::apiResource('promos', PromoController::class)->only(['store', 'update', 'destroy']);
    Route::prefix('promos')->group(function () {
        Route::name('promos.')->group(function () {
            Route::controller(PromoController::class)->group(function (){
                Route::match(['put', 'patch'], '{promo}/cancel', 'cancel')->name('cancel');
                Route::match(['put', 'patch'], '{promo}/continue', 'continue')->name('continue');
                Route::post('{promo}/recipients', 'attachRecipients')->name('recipients.attach');
                Route::delete('{promo}/recipients', 'detachRecipients')->name('recipients.detach');
                Route::post('{promo}/applicable-products', 'attachApplicableProducts')->name('applicable-products.attach');
                Route::delete('{promo}/applicable-products', 'detachApplicableProducts')->name('applicable-products.detach');
                Route::post('{promo}/applicable-categories', 'attachApplicableCategories')->name('applicable-categories.attach');
                Route::delete('{promo}/applicable-categories', 'detachApplicableCategories')->name('applicable-categories.detach');
            });
        });
    });

    Route::apiResource('coupons', CouponController::class)->only(['store', 'update', 'destroy']);
    Route::prefix('coupons')->group(function () {
        Route::name('coupons.')->group(function () {
            Route::controller(CouponController::class)->group(function (){
                Route::match(['put', 'patch'], '{coupon}/cancel', 'cancel')->name('cancel');
                Route::match(['put', 'patch'], '{coupon}/continue', 'continue')->name('continue');
                Route::post('{coupon}/recipients', 'attachRecipients')->name('recipients.attach');
                Route::delete('{coupon}/recipients', 'detachRecipients')->name('recipients.detach');
                Route::post('{coupon}/applicable-products', 'attachApplicableProducts')->name('applicable-products.attach');
                Route::delete('{coupon}/applicable-products', 'detachApplicableProducts')->name('applicable-products.detach');
                Route::post('{coupon}/applicable-categories', 'attachApplicableCategories')->name('applicable-categories.attach');
                Route::delete('{coupon}/applicable-categories', 'detachApplicableCategories')->name('applicable-categories.detach');
            });
        });
    });

    Route::prefix('checkout')->group(function () {
        Route::name('checkout.')->group(function () {
            Route::controller(CheckoutController::class)->group(function (){
                Route::get('', 'index' )->name('checkout.index');
                Route::get('summary', 'index' )->name('checkout.summary');
                Route::match(['put', 'patch'], 'delivery-address/{address}', 'upsertDeliveryAddress' )->name('checkout.devlivery-address.upsert');
                Route::match(['put', 'patch'], 'coupon', 'addCoupon' )->name('checkout.coupon.add');
                Route::match(['put', 'patch'], 'payment-gateway/{payment_gateway}', 'upsertPaymentGateway' )->name('checkout.payment-gateway.upsert');
                Route::match(['put', 'patch'], 'logistic-compnay/{logistic_company}', 'upsertLogisticCompany' )->name('checkout.logistic-company.upsert');
                Route::post( 'confirm-order', 'confirmOrder')->name('confirmOrder');
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

    Route::prefix('store')->group(function () {
        Route::name('store.')->group(function () {

            Route::middleware(['merge.store.addressable.filter'])->group(function () {
                Route::apiResource('address', AddressController::class)->only(['index','store']);
            });

            Route::middleware(['merge.work.stores.id.filter'])->group(function () {
                Route::apiResource('staff', UserController::class)->only(['index']);
            });

            Route::controller(StoreController::class)->group(function(){
                Route::get('address', 'showAddress')->name('address.show');
                Route::match(['put', 'patch'],'address', 'updateAddress')->name('address.update');
            });
            
            Route::middleware(['merge.store.id.filter'])->group(function () {
                Route::Resource('store-invitations', StoreInvitationController::class);
                Route::post('accept-invitation', [StoreInvitationController::class, 'acceptInvitation']);
                Route::post('decline-invitation', [StoreInvitationController::class, 'declineInvitation']);
                Route::apiResource('products', ProductController::class);
                Route::apiResource('promos', PromoController::class);
                Route::apiResource('coupons', CouponController::class);
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

    Route::apiResource('users', UserController::class)->except(['store', 'update', 'destroy']);
    Route::prefix('users/{user}/')->group(function () {
        Route::name('users.roles.')->group(function () {
            Route::controller(UserController::class)->group(function (){
                Route::get('roles', 'roles')->name('index');
                Route::match(['put', 'patch'], 'assign-role/{role}',  'assignRole')->name('assign');
                Route::delete('/remove-role/{role}', 'removeRole')->name('remove');
            });

        });
    });

    Route::apiResource('user-payments', PaymentController::class);


    Route::apiResource('plans', PlanController::class)->only(['store', 'update', 'destroy']);

    Route::prefix('plans/{plan}')->group(function () {
        Route::name('plans.')->group(function () {
            Route::controller(PlanController::class)->group(function (){
                Route::post('features', 'attachFeature')->name('features.attach');
                Route::delete('features', 'detachFeature')->name('features.detach');
            });
        });
    });

    Route::controller(PlanController::class)->group(function() {

    });

    Route::apiResource('features', FeatureController::class)->only(['store', 'update', 'destroy']);

    Route::controller(SubscriptionController::class)->group(function () {
        Route::post('subscriptions', 'subscribe')->name('subscriptions');
        Route::post('renew-plan', 'renewPlan')->name('renew-plan');
        Route::patch('switch-plan', 'switchPlan')->name('switch-plan');
        Route::patch('cancel-plan', 'cancelPlan')->name('cancel-plan');
    });
    Route::controller(SearchController::class)->group(function() {
        Route::get('product-search',  'productSearch')->name('product-search');
        Route::get('category-search',  'categorySearch')->name('category-search');
        Route::get('order-search',  'orderSearch')->name('order-search');
        Route::get('user-search',  'userSearch')->name('user-search');
        Route::get('store-search',  'storeSearch')->name('store-search');
    });

    Route::apiResource('settings', SettingController::class);


});

// End of Auth Routes

/**
 *  <----- The Public Routes ----->
 *  These are routes that do not require authentication
 *  or authorization. They include mostly GET request
 */

Route::controller(RegisterNewUserController::class)->group(function() {
    Route::post('password-less',  'registerWithOnlyEmail')->name('password-less');
    Route::post('register',  'register')->name('register');
    Route::put('resend','resend')->name('resend');
    Route::post('account/verify', 'verifyAccount')->name('user.verify');
});

Route::controller(LoginController::class)->group(function() {
    Route::post('login', 'login')->name('login');
    Route::post('login-via-otp',  'loginViaOTP')->name('login.viaOTP');
});

Route::controller(PasswordResetController::class)->group(function() {
    Route::post('forgot-password',  'sendPasswordResetCode')->name('forgot-password');
    // Route::post('reset-password-token',  'verifyOtp')->name('reset-password-token');
    Route::post('verify-otp',  'verifyOTP')->name('verify-otp');
    Route::put('new-password', 'resetPassword')->name('new-password');
    Route::put('resend-code','resendCode')->name('resend-code');
});

Route::controller(SearchController::class)->group(function (){
    Route::get('search',  'search')->name('search');
    Route::get('searchable',  'searchable')->name('searchable');
});

Route::apiResource('attributes', AttributeController::class)->only(['index', 'show']);
Route::apiResource('addresses', AddressController::class)->only(['index', 'show']);

Route::middleware([CheckoutSummarySessionCleaner::class])->group(function () {
    Route::prefix('cart-items/')->group(function () {
        Route::name('cart-items.')->group(function () {
            Route::controller(CartController::class)->group(function() {
                Route::get('',  'items')->name('index')->withoutMiddleware([CheckoutSummarySessionCleaner::class]);
                Route::post('',  'add')->name('add');
                Route::match(['put', 'patch'], '{item}',  'increment')->name('increment');
                Route::delete('{item}',  'decrement')->name('decrement');
                Route::delete('{item}/remove',  'remove')->name('remove');
                Route::delete('',  'clear')->name('clear');
            });
        });
    });
});

// Route::apiResource('carts', CartController::class)->only(['index', 'show']);
Route::apiResource('coupons', CouponController::class)->only(['index', 'show']);
Route::prefix('coupons/{coupon}/')->group(function () {
    Route::name('coupons.')->group(function () {
        Route::controller(CouponController::class)->group(function (){
            Route::get('products', 'productsIndex')->name('products.index');
        });
    });
});

Route::apiResource('categories', CategoryController::class )->only(['index', 'show']);
Route::apiResource('countries', CountryController::class)->only(['index', 'show']);
Route::apiResource('discount-types', DiscountTypeController::class)->only(['index', 'show']);
Route::apiResource('images', ImageController::class)->only(['index', 'show']);
Route::apiResource('notification-preferences', NotificationPreferenceController::class)->only(['index', 'show']);
Route::apiResource('payment-gateways', PaymentGatewayController::class)->only(['index', 'show']);
Route::apiResource('logistic-companies', LogisticCompanyController::class)->only(['index', 'show']);
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

Route::apiResource('plans', PlanController::class)->only(['index', 'show']);
Route::apiResource('features', FeatureController::class)->only(['index', 'show']);

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

