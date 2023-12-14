<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Address;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\PaymentGateway;
use App\Services\CheckoutService;
class CheckoutController extends Controller
{

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
        $items_total_price = 0;
        $items = auth()->user()->cartItems;
        
        if(!session()->has(CheckoutService::DEFAULT_INSTANCE))
        {
            $items->each( function($item) use (&$items_total_price){
                $items_total_price += $item->total_price;
            });

            $payment_gateway = PaymentGateway::where('is_default', true)
                               ->where('is_active', true)->first();

            $order = new Order([
                'user_id' => auth()->user()->id,
                'address_id' => auth()->user()->addresses()->where('is_preferred', true)->first()->id ?? null,
                'price' => $items_total_price,
            ]);

            session([
                CheckoutService::DEFAULT_INSTANCE => [
                'order' =>  $order,
                'items' => $items,
                'payment_gateway_id' => $payment_gateway->id ?? null,
                ]
            ]);
        }
        else {
            
            // recaculate items total price + coupon adjusted price if applied
        }

        return session(CheckoutService::DEFAULT_INSTANCE);
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
        session([CheckoutService::DEFAULT_INSTANCE => $updatable_summary]);

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

        session([CheckoutService::DEFAULT_INSTANCE => $updatable_summary]);

        return response()->json([
            "data" => $this->summary(),
            "message" => "Payment gateway updated updated successfully"
        ], 200);

    }

    /**
     * add coupon to checkout
     * 
     * @method PUT || PATCH
     * @param string $code
     * @return \Illuminate\Http\Response\Json
     */
    public function addCoupon($coupon)
    {  
        $coupon = Coupon::where('code', $coupon)->first();

        /**
         * Check if the coupon is valid
         * to be valid, 
         *  the coupon is not cancelled 
         *  the coupon coupon validity date has not passed
         *  the coupon has not been used
         * 
         * This condition is designed in the coupon model as cast attribute
         */

         $coupon_is_valid = !is_null($coupon) || ($coupon->is_valid);


        // check if any of the items in the chart is couponable.

        $updatable_summary = $this->summary();                                                                                                                                                                                                                           

        foreach ($updatable_summary['items'] as $key => $item) 
        {
            if(!$coupon_is_valid)
            {
                break;
            }

            if(!is_null($product = $item->product))
            {
                $coupon_is_applicable = !is_null($product->coupons()->where('couponable_id', $product->id)->where('couponable_type', $product::class )->first());
                
                $store = $product->store;
                $coupon_is_applicable = $coupon_is_applicable || !is_null($store->coupons()->where('store_id', $store->id)->first());
               
                $coupon_is_applicable = $coupon_is_applicable || $coupon->store_id == null;

                if($coupon_is_applicable)
               {
                    $updatable_summary['order']['coupon_id'] = $coupon->id;
                    break;
               }
            }
        }

        if(!$coupon_is_valid || !$coupon_is_applicable )
        {
            return response()->json([
                "message" => "Invalid coupon",
                "errors"=> [
                    "coupon" => [
                        "Coupon added failed."
                    ],
                ]
            ], 422);  
        }

        session([CheckoutService::DEFAULT_INSTANCE => $updatable_summary]);

        return response()->json([
            "data" => $this->summary(),
            "message" => "Coupon added successfully"
        ], 200);

    }

    /**
     * Placing the order 
     * 
     * @method POST
     */
    public function placeOrder(){

    }
}
