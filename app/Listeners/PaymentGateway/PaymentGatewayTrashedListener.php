<?php

namespace App\Listeners\PaymentGateway;

use App\Events\PaymentGateway\PaymentGatewayTrashed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class PaymentGatewayTrashedListener
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
    public function handle(PaymentGatewayTrashed $event): void
    {
        //
    }
}
