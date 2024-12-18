<?php

namespace App\Listeners\Store;

use App\Events\Store\StoreCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class StoreCreatedListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(StoreCreated $event): void
    {
        $store = $event->store;
        $store->url = \config('sanmtos.base_url').'/stores/'.$store->slug;
        $store->owner->assignRole('store-admin');
        $store->save();
    }
}
