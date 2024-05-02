<?php

namespace App\Listeners\Coupon;

use App\Events\Coupon\CouponUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CouponUpdatedListener
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
    public function handle(CouponUpdated $event): void
    {
        //
    }
}
