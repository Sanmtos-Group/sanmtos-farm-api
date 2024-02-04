<?php

namespace App\Interfaces;

use App\Models\Payment;
use Illuminate\Http\Request;
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
    public function verify(Request $request, Payment $payment);

}