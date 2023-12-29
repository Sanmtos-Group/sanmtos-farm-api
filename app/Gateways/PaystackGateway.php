<?php

namespace App\Gateways;

use App\Interfaces\Payable;
use App\Models\Payment;
class PaystackGateway implements Payable
{
    /**
     * Make the payment
     * 
     * @implements App\Interfaces\Payable\Pay
     * @param App\Models\Payment $payment;
     */
    public function pay(Payment $payment){

        /**
         * Paystack make payment implementation goes here 
         */
        return $payment;
    }

    /**
     * Verify the payment
     * 
     * @implements App\Interfaces\Payable\verify
     * @param App\Models\Payment $payment;
     */
    public function verify(Payment $payment){
        /**
         * Paystack verify payment implementation goes here 
         */

         return $payment;
    }
}