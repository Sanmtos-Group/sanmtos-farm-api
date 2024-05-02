<?php

namespace App\Listeners\Rating;

use App\Events\Rating\RatingTrashed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RatingTrashedListener
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
    public function handle(RatingTrashed $event): void
    {
        //
    }
}
