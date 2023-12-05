<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderProduct;
class CheckoutController extends Controller
{
    
    public function summary(){
        
        $cart_items = auth()->user()->cartItems ;
        // $order = new Order();
        // $order->user_id = auth()->user()->id;

        $order = Order::factory()->make([
            'user_id' => auth()->user()->id,
            'address_id' => auth()->user()->addresses()->where('is_preferred', true)->first()->id,
            'status' => 'checking out',
        ]);

        return response()->json([
            "data" => [
                'order' => $order->toArray(),
                'items' => $cart_items,
            ],
            "message" => "The provided credentials do not match in our records."
        ], 200);
    }
}
