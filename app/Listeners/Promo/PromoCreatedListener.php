<?php

namespace App\Listeners\Promo;

use App\Events\Promo\PromoCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class PromoCreatedListener
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
    public function handle(PromoCreated $event): void
    {
        //
    }
}
