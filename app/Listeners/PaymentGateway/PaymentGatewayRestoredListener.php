<?php

namespace App\Listeners\PaymentGateway;

use App\Events\PaymentGateway\PaymentGatewayRestored;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class PaymentGatewayRestoredListener
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
    public function handle(PaymentGatewayRestored $event): void
    {
        //
    }
}
