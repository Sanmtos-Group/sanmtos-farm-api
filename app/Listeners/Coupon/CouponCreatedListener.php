<?php

namespace App\Listeners\Coupon;

use App\Events\Coupon\CouponCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CouponCreatedListener
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
    public function handle(CouponCreated $event): void
    {
        //
    }
}
