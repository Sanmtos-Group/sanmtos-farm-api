<?php

namespace App\Listeners\Rating;

use App\Events\Rating\RatingRestored;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RatingRestoredListener
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
    public function handle(RatingRestored $event): void
    {
        //
    }
}
