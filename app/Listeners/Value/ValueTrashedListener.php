<?php

namespace App\Listeners\Value;

use App\Events\Value\ValueTrashed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ValueTrashedListener
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
    public function handle(ValueTrashed $event): void
    {
        //
    }
}
