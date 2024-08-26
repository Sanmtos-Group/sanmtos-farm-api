<?php

namespace App\Listeners\Value;

use App\Events\Value\ValueDeleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ValueDeletedListener
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
    public function handle(ValueDeleted $event): void
    {
        //
    }
}
