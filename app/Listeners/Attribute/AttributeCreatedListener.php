<?php

namespace App\Listeners\Attribute;

use App\Events\Attribute\AttributeCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AttributeCreatedListener
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
    public function handle(AttributeCreated $event): void
    {
        //
    }
}
