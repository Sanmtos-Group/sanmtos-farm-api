<?php

namespace App\Listeners\Attribute;

use App\Events\Attribute\AttributeUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AttributeUpdatedListener
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
    public function handle(AttributeUpdated $event): void
    {
        //
    }
}
