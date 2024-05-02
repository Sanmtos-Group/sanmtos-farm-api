<?php

namespace App\Listeners\Rating;

use App\Events\Rating\RatingUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RatingUpdatedListener
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
    public function handle(RatingUpdated $event): void
    {
        //
    }
}
