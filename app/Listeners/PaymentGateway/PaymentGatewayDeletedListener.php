<?php

namespace App\Listeners\PaymentGateway;

use App\Events\PaymentGateway\PaymentGatewayDeleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class PaymentGatewayDeletedListener
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
    public function handle(PaymentGatewayDeleted $event): void
    {
        //
    }
}
