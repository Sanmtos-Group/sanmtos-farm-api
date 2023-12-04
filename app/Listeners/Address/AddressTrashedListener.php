<?php

namespace App\Listeners\Address;

use App\Events\Address\AddressTrashed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AddressTrashedListener
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
    public function handle(AddressTrashed $event): void
    {
        //
    }
}
