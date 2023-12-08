<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderProduct;
class CheckoutController extends Controller
{
    const DEFAULT_INSTANCE = 'order-summary';

    /**
     * Checking out the cart 
     *  
     * @method GET
     * @return \Illuminate\Http\Response\Json
     */
    public function index()
    {
        return response()->json([
            "data" => $this->summary(),
            "message" => "Checkout Summary "
        ], 200);
    }

    /**
     * Get the cart summary 
     * @return array  
     */
    public function summary()
    {
        
        if(!session()->has(self::DEFAULT_INSTANCE))
        {
            $items = auth()->user()->cartItems;
            $items_total_price = 0;
            $payment_gateway = PaymentGate::where('is_default', true)
                               ->where('is_active', true)->first();

            $items->each( function($item) use (&$items_total_price){
                $items_total_price += $item->total_price;
            });

            $order = new Order([
                'user_id' => auth()->user()->id,
                'address_id' => auth()->user()->addresses()->where('is_preferred', true)->first()->id ?? null,
                'price' => $items_total_price,
            ]);

            session([
                self::DEFAULT_INSTANCE => [
                'order' =>  $order,
                'items' => $items,
                'payment_gateway_id' => $payment_gateway->id ?? null,
                ]
            ]);
        }

        return session(self::DEFAULT_INSTANCE);
    }

    /**
     * update or insert the delivery address
     * 
     * @method PUT || PATCH
     * @param App\Models\Address $address
     * @return \Illuminate\Http\Response\Json
     */
    public function upsertDeliveryAddress(Address $address)
    {
        $new_address = auth()->user()->addresses()->where('id',$address->id)->first();
        
        if(is_null($new_address))
        {
            return response()->json([
                "message" => "Failed to update delivery address",
                "errors"=> [
                    "adddress" => [
                        "Wrong user address supplied."
                    ],
                ]
            ], 422);  
        }

        $updatable_summary = $this->summary();
        $updatable_summary['order']->address_id = $new_address->id;
        session([self::DEFAULT_INSTANCE => $updatable_summary]);

        return response()->json([
            "data" => $this->summary(),
            "message" => "Delivery address updated successfully"
        ], 200);

    }

    /**
     * Update or insert the payment gateway
     * 
     * @method PUT || PATCH
     * @param App\Models\PaymentGateway $payment_gateway
     * @return \Illuminate\Http\Response\Json
     */
    public function upsertPaymentGateway(PaymentGateway $payment_gateway)
    {        
        if(!($payment_gateway->is_active))
        {
            return response()->json([
                "message" => "Failed to update payment payment",
                "errors"=> [
                    "adddress" => [
                        "Payment gateway is currently not active."
                    ],
                ]
            ], 422);  
        }

        $updatable_summary = $this->summary();
        $updatable_summary['payment_gateway_id'] = $payment_gateway->id;

        session([self::DEFAULT_INSTANCE => $updatable_summary]);

        return response()->json([
            "data" => $this->summary(),
            "message" => "Payment gateway updated updated successfully"
        ], 200);

    }
}
