<?php

namespace App\Listeners\Rating;

use App\Events\Rating\RatingCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RatingCreatedListener
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
    public function handle(RatingCreated $event): void
    {
        //
    }
}
