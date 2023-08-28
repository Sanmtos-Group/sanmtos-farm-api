<?php

namespace App\Listeners\Attribute;

use App\Events\Attribute\AttributeRestored;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AttributeRestoredListener
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
    public function handle(AttributeRestored $event): void
    {
        //
    }
}
