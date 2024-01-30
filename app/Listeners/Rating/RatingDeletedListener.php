<?php

namespace App\Listeners\Rating;

use App\Events\Rating\RatingDeleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RatingDeletedListener
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
    public function handle(RatingDeleted $event): void
    {
        //
    }
}
