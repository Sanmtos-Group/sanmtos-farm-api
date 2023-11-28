<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderProduct;
class CheckoutController extends Controller
{
    
    public function index(){
        
        $cart_items = auth()->user()->cartItems ;
        $order = new Order ();
        
    }
}
