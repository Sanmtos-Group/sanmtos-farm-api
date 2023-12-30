<?php

namespace App\Gateways;

use App\Interfaces\Payable;
use App\Models\Payment;

use Illuminate\Support\Facades\Redirect;
use Paystack;
class PaystackGateway implements Payable
{
    /**
     * Make the payment
     * 
     * @implements App\Interfaces\Payable\Pay
     * @param App\Models\Payment $payment;
     */
    public function pay(Payment $payment)
    {

        try{

            /**
             *  In the case where you need to pass the data from your 
             *  controller instead of a form
             *  Make sure to send:
             *  required: email, amount, reference, orderID(probably)
             *  optionally: currency, description, metadata
             *  e.g:
             *  
             */
            $data = array(
                "amount" => $payment->amount,
                "reference" => $payment->id,
                "email" => $payment->user->email,
                "currency" => "NGN",
                "orderID" => $payment->paymentable_id,
            );
            
            return Paystack::getAuthorizationUrl($data); 
            // return Paystack::getAuthorizationUrl($data)->redirectNow();
        }catch(\Exception $e) {
            throw $e;
        }     
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