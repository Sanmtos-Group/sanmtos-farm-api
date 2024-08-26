<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Str;

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

        \App\Events\StoreInvitation\StoreInvitationCreated::class => [
            \App\Listeners\StoreInvitation\StoreInvitationCreatedListener::class,
        ],

        \App\Events\Address\AddressCreated::class => [
            \App\Listeners\Address\AddressCreatedListener::class,
        ],

        \App\Events\Address\AddressUpdated::class => [
            \App\Listeners\Address\AddressUpdatedListener::class,
        ],

    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        Event::listen('eloquent.attaching: *', function ($model, $relationName, $pivotIds, $pivotAttributes) {
            // Add logic before attaching the pivot record
            dd("james");
            foreach ($pivotAttributes as &$attributes) {
                $attributes['id'] = Str::uuid(); // Set a UUID as the pivot ID
            }
        });                 

    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
