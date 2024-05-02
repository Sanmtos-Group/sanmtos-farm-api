<?php

namespace App\Listeners\PaymentGateway;

use App\Events\PaymentGateway\PaymentGatewayCreated;
use App\Models\PaymentGateway;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class PaymentGatewayCreatedListener
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
    public function handle(PaymentGatewayCreated $event): void
    {
        $payment_gateway = $event->payment_gateway;

        if(PaymentGateway::count() <= 1)
        {
            $payment_gateway->is_active = true;
            $payment_gateway->is_default = true;
            $payment_gateway->save();
        }
        elseif ($payment_gateway->is_default) 
        {
            $payment_gateway->is_active = true;
            $payment_gateway->save();

            PaymentGateway::where('id', '<>', $payment_gateway->id)
            ->update(['is_default' => false]);
        }
    }
}
