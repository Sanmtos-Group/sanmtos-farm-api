<?php

namespace App\Http\Controllers;

use App\Enums\VATEnum;
use App\Handlers\PaymentHandler;
use App\Models\Address;
use App\Models\Coupon;
use App\Models\LogisticCompany;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\PaymentGateway;
use App\Models\Payment;
use App\Models\Setting;
use App\Services\CheckoutService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class CheckoutController extends Controller
{

    private $vat;

    public function __construct()
    {
        $vat_setting = Setting::where('key', VATEnum::Key->value)->whereNull('store_id')->first();      
        $this->vat = (float) (!is_null($vat_setting) ? $vat_setting->value : VATEnum::Value->value);
        $this->vat =(float) number_format($this->vat, 2);
    }   

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
            'status' => 'OK',
            "message" => "Checkout Summary"
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
        $address =  auth()->user()->addresses()->where('is_preferred', true)->first() ?? null;
        $payment_gateway = PaymentGateway::where('is_default', true)
        ->where('is_active', true)->first();

        $logistic_company = LogisticCompany::where('is_default', true)
        ->where('is_active', true)->first();
                
        $delivery_fee = 2500;
      
        if(!session()->has(CheckoutService::DEFAULT_INSTANCE))
        {
            $items->each(function($item) use (&$items_total_price){
                $items_total_price += $item->total_price;
            });

            $price = $items_total_price + $delivery_fee;
            $order = new Order([
                'user_id' => auth()->user()->id,
                'address_id' => $address->id ?? null,
                'price' => $items_total_price,
                'delivery_fee' => $delivery_fee,
                'vat'=> $this->vat,
                'total_price' => $price + ($this->vat/100 * $price) ,
            ]);

            session([
                CheckoutService::DEFAULT_INSTANCE => [
                'order' =>  $order,
                'items' => $items,
                'payment_gateway_id' => $payment_gateway->id ?? null,
                'logistic_company_id' => $logistic_company->id ?? null,
                ]
            ]);
        }
        else {
            $updatable_summary =  session(CheckoutService::DEFAULT_INSTANCE);
            if(is_null($updatable_summary['order']->address_id ?? null))
                $updatable_summary['order']->address_id = $address->id ?? null;

            if(is_null($updatable_summary['order']->delivery_fee ?? null))
            $updatable_summary['order']->delivery_fee = $delivery_fee ?? null;

            if(is_null($updatable_summary['payment_gateway_id'] ?? null))
                $updatable_summary['payment_gateway_id'] = $payment_gateway->id ?? null;
                
            session([CheckoutService::DEFAULT_INSTANCE => $updatable_summary]);
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
        $new_address = auth()->user()->addresses()->where('id', $address->id)->first();
        
        if(is_null($new_address))
        {
            return response()->json([
                'data' => null,
                'status' => 'FAILED',
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
            'status' => 'OK',
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
                'data' => null,
                'status' => 'FAILED',
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
            'status' => 'OK',
            "message" => "Payment gateway updated updated successfully"
        ], 200);

    }

    /**
     * Update or insert the logistic company 
     * 
     * @method PUT || PATCH
     * @param App\Models\LogisticCompany $logistic_company
     * @return \Illuminate\Http\Response\Json
     */
    public function upsertLogisticCompany(LogisticCompany $logistic_company)
    {        
        if(!($logistic_company->is_active))
        {
            return response()->json([
                'data' => null,
                'status' => 'FAILED',
                "message" => "Failed to update logistic compnay",
                "errors"=> [
                    "adddress" => [
                        "Logistic company is currently not active."
                    ],
                ]
            ], 422);  
        }

        $updatable_summary = $this->summary();
        $updatable_summary['logistic_company_id'] = $logistic_company->id;

        session([CheckoutService::DEFAULT_INSTANCE => $updatable_summary]);

        return response()->json([
            "data" => $this->summary(),
            'status' => 'OK',
            "message" => "Logistic company updated updated successfully"
        ], 200);

    }

    /**
     * add coupon to checkout
     * 
     * @method PUT || PATCH
     * @param string $code
     * @return \Illuminate\Http\Response\Json
     */
    public function addCoupon(Request $request)
    {  
        $coupon = Coupon::where('id', $request->coupon)
        ->orWhere('code', $request->coupon)->first();

        // check if the coupon is applicable
        $coupon_is_valid = !is_null($coupon) && ($coupon->is_valid ?? false);
        
        $updatable_summary = $this->summary(); 

        // checking if any of the items in the chart is couponable.
        foreach ($updatable_summary['items'] as $key => $item) 
        {
            if(!$coupon_is_valid)
            {
                break;
            }

            if(!is_null($product = $item->product))
            {
                // check if the coupon is attached to the product
                $coupon_is_applicable = !is_null($coupon->products()->where('products.id', $product->id)->first());
                // check if the coupon and the product belongs to the same store               
                $coupon_is_applicable = $coupon_is_applicable && $coupon->store_id == $product->store_id;

                if($coupon_is_applicable)
               {
                    $discount_price = $product->price - ($coupon->discount/100 * $product->price);

                    // check if the coupon is bulk applicaton and exact item's quantity is in the cart
                    if($coupon->is_bulk_applicable  && $item->quantity = $coupon->number_of_items)
                    {
                       $discount_price *= $item->quantity;
                    }
                    $updatable_summary['order']['coupon_id'] = $coupon->id;
                    $updatable_summary['order']['total_price'] = $updatable_summary['order']['price'] - $discount_price;

                    break;
               }
            }
        }

        // checking if coupon is not added successfully
        if(!$coupon_is_valid || !$coupon_is_applicable?? false)
        {
            $items_total_price = 0;
            $items = auth()->user()->cartItems;
            $items->each( function($item) use (&$items_total_price){
                $items_total_price += $item->total_price;
            });

            // reset order's price, total price and coupon
            $updatable_summary['order']['price'] = $items_total_price;
            $updatable_summary['order']['total_price'] = $items_total_price;
            $updatable_summary['order']['coupon_id'] = null;
            session([CheckoutService::DEFAULT_INSTANCE => $updatable_summary]);

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
            'status' => 'OK',
            "message" => "Coupon added successfully"
        ], 200);

    }

    /**
     * Confirming the order 
     * 
     * @method POST
     */
    public function confirmOrder(){
        $errors = [];
        $summary = $this->summary();
        try {

            if(!array_key_exists('order', $summary))
            {
                $errors['order'] = 'No initialized order';
            }
            else {
                
                if(empty($summary['order']->user_id))
                    $errors['user_id'] = 'No authenticated user';

                if(empty($summary['order']->address_id))
                    $errors['address_id'] = 'Address is required';

                if(empty($summary['order']->total_price))
                    $errors['total_price'] = 'No items total price';
            }

            if(!array_key_exists('payment_gateway_id', $summary))
            {
                $errors['payment_gateway_id'] = 'Please select payment gateway';
            }

            if(!array_key_exists('logistic_company_id', $summary))
            {
                $errors['logistic_company_id'] = 'Please select desired logistic company for delivery';
            }

            if(!empty($errors))
            {
                throw new Exception("Order confirmation failed", 1);
            }

      
            DB::beginTransaction();

            $order = $summary['order'];
            $order->number = strtoupper('SF'.date('YMd')).(Order::where('created_at', 'like' ,'%'.date('Y-m-d').'%')->count() + 1);
            $metadata = new class() {};
            $metadata->address = $order->address->toArray();
            $order->metadata = $metadata;
            $order->save();
            
            $items = $summary['items'];
            $orderables = [];

            $items->each(function($item) use (&$orderables){
                $orderables[] = [
                    'orderable_id' =>  $item->cartable_id,
                    'orderable_type' =>  $item->cartable_type,
                    'quantity' =>  $item->quantity,
                    'price' =>  $item->price,
                    'total_price' =>  $item->total_price
                ];
            });
            
            $order->orderables()->createMany($orderables);
            
            $payment = $order->payment()->create([
                'user_id' => auth()->user()->id,
                'amount' => intval(ceil($order->total_price)),
                'transaction_reference' =>  Payment::genTranxRef(),
                'gateway_id' => $summary['payment_gateway_id'],

            ]);

            $payment_handler = new PaymentHandler();

            $payment_gateway_handler = $payment_handler->initializePaymentGateway($payment->gateway->name);
            $payment_url = $payment_gateway_handler->pay($payment) ;
            
            DB::commit();

            session()->forget(CheckoutService::DEFAULT_INSTANCE);
            auth()->user()->cartItems()->delete();

            return response()->json([
                "data" => [
                    'payment_id' => $payment->id,
                    'payment_url' => $payment_url 
                ],
                "message" => "Proceed to pay via ".$payment->gateway->name
            ], 200);

        } catch (\Throwable $th) {

            DB::rollBack();

            return response()->json([
                "data" => [],
                'status' => 'FAILED',
                "message" => $th->getMessage(),
                'errors' => $errors,
            ], 422);
        }
        
    }
}
