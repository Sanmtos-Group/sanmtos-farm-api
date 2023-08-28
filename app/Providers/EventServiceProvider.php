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
         *  Attribute Events and listeners;
         */
        \App\Events\Attribute\AttributeCreated::class => [
            \App\Listeners\Attribute\AttributeCreatedListener::class,
        ],

        \App\Events\Attribute\AttributeUpdated::class => [
            \App\Listeners\Attribute\AttributeUpdatedListener::class,
        ],

        \App\Events\Attribute\AttributeTrashed::class => [
            \App\Listeners\Attribute\AttributeTrashedListener::class,
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
