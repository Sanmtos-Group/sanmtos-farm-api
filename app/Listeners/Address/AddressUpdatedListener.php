<?php

namespace App\Listeners\Address;

use App\Events\Address\AddressUpdated;
use App\Jobs\UpdateAddressMetadata;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Bus\Queueable;

class AddressUpdatedListener
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
    public function handle(AddressUpdated $event): void
    {
        $address = $event->address;

        if($address->wasChanged(['address', 'lga', 'state', 'country_id']))
        {
            UpdateAddressMetadata::dispatch($address);
        }
    }
}
