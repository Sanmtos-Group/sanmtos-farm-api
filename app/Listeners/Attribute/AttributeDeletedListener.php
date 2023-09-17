<?php

namespace App\Listeners\Attribute;

use App\Events\Attribute\AttributeDeleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AttributeDeletedListener
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
    public function handle(AttributeDeleted $event): void
    {
        //
    }
}
