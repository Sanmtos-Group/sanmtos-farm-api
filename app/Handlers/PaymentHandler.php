<?php

namespace App\Handlers;

use App\Gateways\PaystackGateway;
class PaymentHandler {
    /**
     * @var App\Gateways\PaymentGateway $payment_gateway
     */
    private  $payment_gateway;
    

    /**
     *  Create an instance of payment handler
     *  @param string $gateway
     *  
     *  @return void
     */
    public function __construct(string $gateway=""){

        if(is_string($gateway) && !empty($gateway))
        {
            $this->initializePaymentGateway($gateway);
        }
        else {
            $this->payment_gateway = NULL;
        }
    }

     /**
     *  Create an instance of payment handler
     *  @param string $gateway
     *  
     *  @return void
     */
    public function initializePaymentGateway(string $gateway=""){
        switch (strtolower($gateway)) {

            case 'paystack':
                $this->payment_gateway = new PaystackGateway;
                break;

            default:
                $this->payment_gateway = NULL;
                throw new Exception("Unsupported payment gateway...!!! ".$gateway, 1);
                # code...
                break;
        }
    }

    /**
     *  Get the initialized payment gateway
     *  @return \App\Gateways\any_initialized_payment_gateway $payment_gateway
     */
    public function getPaymentGateway(){
        return $this->payment_gateway;
    }
}