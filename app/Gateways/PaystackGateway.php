<?php

namespace App\Gateways;

use App\Interfaces\Payable;
use App\Models\Payment;
use Illuminate\Http\Request;
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

            if(is_null($payment->transaction_reference) || empty($payment->transaction_reference))
            {
                $payment->transaction_reference =  Payment::genTranxRef();
            }

            $data = array(
                // "quantity" => 1,
                "amount" => $payment->amount * 100,
                "reference" => $payment->transaction_reference,
                "email" => $payment->user->email,
                "first_name" => $payment->user->first_name,
                "last_name" => $payment->user->last_name,
                "currency" => "NGN",
                "orderID" => $payment->paymentable_id,
                "callback_url" => route('api.payments.callback'),
                'metadata' => [
                    'description' => $payment->description,
                ]
            );

            $payment->gateway_checkout_url = Paystack::getAuthorizationUrl($data)->url;
            $payment->save();
            
            return $payment->gateway_checkout_url; 
            // return Paystack::getAuthorizationUrl($data)->redirectNow();
        }catch(\Exception $e) {
            throw $e;
        }     
    }
    
    /**
     * Verify the payment
     * 
     * @implements App\Interfaces\Payable\verify
     * @param Illuminate\Http\Request|array<int,string> $request
     * @param App\Models\Payment $payment
     */
    public function verify(Request $request, Payment $payment)
    {
        
        if(!$request->has('trxref') || !$request->has('reference'))
        {
            $request->query->set('trxref', $payment->transaction_reference);
            $request->query->set('reference', $payment->transaction_reference);
        }

        $response = Paystack::getPaymentData();
        $response = json_decode(json_encode($response));


        if($response->status == true 
            && $response->data->reference===$payment->transaction_reference 
            && $response->data->amount==$payment->amount * 100 
        ){
            $payment->transaction_status = 'successful';
            $payment->currency = $response->data->currency;
            $payment->method = $response->data->channel;
            $payment->ip_address = $response->data->ip_address;
            $payment->paid_at = new \DateTime($response->data->paid_at);
            $payment->verified_at = now();
            $payment->metadata = json_encode($response);
            $payment->save();

            return true;
        }

        return false;
    }
}