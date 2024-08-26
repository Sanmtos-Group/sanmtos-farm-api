<?php

namespace App\Listeners\Value;

use App\Events\Value\ValueRestored;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ValueRestoredListener
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
    public function handle(ValueRestored $event): void
    {
        //
    }
}
