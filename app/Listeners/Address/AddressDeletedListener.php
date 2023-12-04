<?php

namespace App\Listeners\Address;

use App\Events\Address\AddressDeleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AddressDeletedListener
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
    public function handle(AddressDeleted $event): void
    {
        //
    }
}
