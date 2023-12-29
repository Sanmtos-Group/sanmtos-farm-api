<?php

namespace App\Interfaces;

use App\Models\Payment;
interface Payable {
    
    /**
     * Make the payment
     * 
     * @param App\Models\Payment $payment;
     */
    public function pay(Payment $payment);

    /**
     * Verify the payment
     * 
     * @param App\Models\Payment $payment;
     */
    public function verify(Payment $payment);

}