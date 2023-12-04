<?php

namespace App\Listeners\Address;

use App\Events\Address\AddressRestored;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AddressRestoredListener
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
    public function handle(AddressRestored $event): void
    {
        //
    }
}
