<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        /**
         *  Address Events and listeners;
         */
        \App\Events\Address\AddressCreated::class => [
            \App\Listeners\Address\AddressCreatedListener::class,
        ],

        \App\Events\Address\AddressDeleted::class => [
            \App\Listeners\Address\AddressDeletedListener::class,
        ],

        \App\Events\Address\AddressRestored::class => [
            \App\Listeners\Address\AddressRestoredListener::class,
        ],

        \App\Events\Address\AddressUpdated::class => [
            \App\Listeners\Address\AddressUpdatedListener::class,
        ],

        \App\Events\Address\AddressTrashed::class => [
            \App\Listeners\Address\AddressTrashedListener::class,
        ],

        /**
         *  Attribute Events and listeners;
         */
        \App\Events\Attribute\AttributeCreated::class => [
            \App\Listeners\Attribute\AttributeCreatedListener::class,
        ],

        \App\Events\Attribute\AttributeDeleted::class => [
            \App\Listeners\Attribute\AttributeDeletedListener::class,
        ],

        \App\Events\Attribute\AttributeRestored::class => [
            \App\Listeners\Attribute\AttributeRestoredListener::class,
        ],

        \App\Events\Attribute\AttributeUpdated::class => [
            \App\Listeners\Attribute\AttributeUpdatedListener::class,
        ],

        \App\Events\Attribute\AttributeTrashed::class => [
            \App\Listeners\Attribute\AttributeTrashedListener::class,
        ],

        /**
         *  Image Events and listeners;
         */
        \App\Events\Image\ImageCreated::class => [
            \App\Listeners\Image\ImageCreatedListener::class,
        ],

        \App\Events\Image\ImageDeleted::class => [
            \App\Listeners\Image\ImageDeletedListener::class,
        ],

        \App\Events\Image\ImageRestored::class => [
            \App\Listeners\Image\ImageRestoredListener::class,
        ],

        \App\Events\Image\ImageUpdated::class => [
            \App\Listeners\Image\ImageUpdatedListener::class,
        ],

        \App\Events\Image\ImageTrashed::class => [
            \App\Listeners\Image\ImageTrashedListener::class,
        ],

        /**
         *  PaymentGateway Events and listeners;
         */
        \App\Events\PaymentGateway\PaymentGatewayCreated::class => [
            \App\Listeners\PaymentGateway\PaymentGatewayCreatedListener::class,
        ],

        \App\Events\PaymentGateway\PaymentGatewayDeleted::class => [
            \App\Listeners\PaymentGateway\PaymentGatewayDeletedListener::class,
        ],

        \App\Events\PaymentGateway\PaymentGatewayRestored::class => [
            \App\Listeners\PaymentGateway\PaymentGatewayRestoredListener::class,
        ],

        \App\Events\PaymentGateway\PaymentGatewayUpdated::class => [
            \App\Listeners\PaymentGateway\PaymentGatewayUpdatedListener::class,
        ],

        \App\Events\PaymentGateway\PaymentGatewayTrashed::class => [
            \App\Listeners\PaymentGateway\PaymentGatewayTrashedListener::class,
        ],


        /**
         *  Product Events and listeners;
         */
        \App\Events\Product\ProductCreated::class => [
            \App\Listeners\Product\ProductCreatedListener::class,
        ],

        \App\Events\Product\ProductUpdated::class => [
            \App\Listeners\Product\ProductUpdatedListener::class,
        ],

        \App\Events\Product\ProductTrashed::class => [
            \App\Listeners\Product\ProductTrashedListener::class,
        ],


        /**
         *  Promo Events and listeners;
         */
        \App\Events\Promo\PromoCreated::class => [
            \App\Listeners\Promo\PromoCreatedListener::class,
        ],

        \App\Events\Promo\PromoUpdated::class => [
            \App\Listeners\Promo\PromoUpdatedListener::class,
        ],

        /**
         *  Role Events and listeners;
         */
        \App\Events\Role\RoleCreated::class => [
            \App\Listeners\Role\RoleCreatedListener::class,
        ],

        \App\Events\Role\RoleUpdated::class => [
            \App\Listeners\Role\RoleUpdatedListener::class,
        ],

        \App\Events\Role\RoleDeleted::class => [
            \App\Listeners\Role\RoleDeletedListener::class,
        ],

        /**
         *  Store Events and listeners;
         */
        \App\Events\Store\StoreCreated::class => [
            \App\Listeners\Store\StoreCreatedListener::class,
        ],

        \App\Events\Store\StoreUpdated::class => [
            \App\Listeners\Store\StoreUpdatedListener::class,
        ],

        \App\Events\Store\StoreTrashed::class => [
            \App\Listeners\Store\StoreTrashedListener::class,
        ],

        /**
         * Authentication Process Events 
         * Map listeners of your choice 
         */

         'Illuminate\Auth\Events\Registered' => [
            // 'App\Listeners\LogRegisteredUser',
        ],
     
        'Illuminate\Auth\Events\Attempting' => [
            // 'App\Listeners\LogAuthenticationAttempt',
        ],
     
        'Illuminate\Auth\Events\Authenticated' => [
            // 'App\Listeners\LogAuthenticated',
        ],
     
        'Illuminate\Auth\Events\Login' => [
            // 'App\Listeners\LogSuccessfulLogin',
            \App\Listeners\Cart\CartSyncToDatabaseOnLoginListener::class,
        ],
     
        'Illuminate\Auth\Events\Failed' => [
            // 'App\Listeners\LogFailedLogin',
        ],
     
        'Illuminate\Auth\Events\Validated' => [
            // 'App\Listeners\LogValidated',
        ],
     
        'Illuminate\Auth\Events\Verified' => [
            // 'App\Listeners\LogVerified',
        ],
     
        'Illuminate\Auth\Events\Logout' => [
            // 'App\Listeners\LogSuccessfulLogout',
        ],
     
        'Illuminate\Auth\Events\CurrentDeviceLogout' => [
            // 'App\Listeners\LogCurrentDeviceLogout',
        ],
     
        'Illuminate\Auth\Events\OtherDeviceLogout' => [
            // 'App\Listeners\LogOtherDeviceLogout',
        ],
     
        'Illuminate\Auth\Events\Lockout' => [
            // 'App\Listeners\LogLockout',
        ],
     
        'Illuminate\Auth\Events\PasswordReset' => [
            // 'App\Listeners\LogPasswordReset',
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
