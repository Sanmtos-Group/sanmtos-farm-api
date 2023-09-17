<?php

namespace App\Listeners\Attribute;

use App\Events\Attribute\AttributeTrashed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AttributeTrashedListener
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
    public function handle(AttributeTrashed $event): void
    {
        //
    }
}
