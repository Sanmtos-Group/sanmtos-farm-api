<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderProduct;
class CheckoutController extends Controller
{
    const MINIMUM_QUANTITY = 1;
    const DEFAULT_INSTANCE = 'checkingout-order';

    public function summary(){
        $order = new Order ();

    }
}
