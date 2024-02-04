<?php

namespace App\Http\Controllers;

use App\Handlers\PaymentHandler;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Paystack;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;
class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $payments = QueryBuilder::for(Payment::class)
        ->defaultSort('created_at')
        ->allowedSorts(
            'transaction_status',
            'amount',
            'method',
            'currency',
            'ip_address',
            'created_at',
            AllowedSort::custom('recent', new \App\Models\Sorts\LatestSort()),
            AllowedSort::custom('oldest', new \App\Models\Sorts\OldestSort()),
        )
        ->allowedIncludes([
            'gateway',
            'user',
            'paymentable'
        ])
        ->paginate()
        ->appends(request()->query());

        $payment_resource =  PaymentResource::collection($payments);
        $payment_resource->with['status'] = "OK";
        $payment_resource->with['message'] = 'Payments retrieved successfully';

        return $payment_resource;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        $payment_resource = new PaymentResource($payment);
        $payment_resource->with['message'] = 'Payment retrieved successfully';

        return  $payment_resource;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payment $payment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePaymentRequest $request, Payment $payment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        //
    }

    /***
     * Handle Payment callback 
     */

    public function callback(Request $request){

        $transaction_reference = null;

        /**
         * Checking for paystack possible transaction reference number
         * 
         */
        if($request->trxref)
        {
            $transaction_reference = $request->trxref;
        }

        elseif($request->reference)
        {
            $transaction_reference = $request->reference;
        }
        // end cheking for paystack

        $payment = Payment::where('transaction_reference', $transaction_reference)->first();

        if(is_null($payment))
        {
            return response()->json([
                'status' => 'FAILED',
                "message" => 'No payment exist with the transaction reference in our record'
            ], 422);

        }

        $payment_handler = new PaymentHandler();
        $payment_gateway_handler = $payment_handler->initializePaymentGateway($payment->gateway->name);

        $is_verified= $payment_gateway_handler->verify($request, $payment);

        $payment->refresh();

        if(!$is_verified){

            return response()->json([
                "data" => $payment,
                "message" => 'Payment verification failed'
            ], 422);
        }


        return response()->json([
            "data" => $payment,
            "message" => 'Payment verified successful'
        ], 200);
    }

    /***
     * Handle Payment webhook 
     */

     public function webhook(Request $request){

        if(!$reuqest->has('HTTP_X_PAYSTACK_SIGNATURE'))
        {
            exit();
        }

                // validate event do all at once to avoid timing attack
        if($request->header('HTTP_X_PAYSTACK_SIGNATURE') !== hash_hmac('sha512', $request->all(), config('paystack.secretKey')))
        {
            exit();
        }

        http_response_code(200);
        header('Content-Type: application/json'); // Set content type if needed

        $transaction_reference = null;

        /**
         * Checking for paystack possible transaction reference number
         * 
         */
        if($request->trxref)
        {
            $transaction_reference = $request->trxref;
        }

        elseif($request->reference)
        {
            $transaction_reference = $request->reference;
        }
        // end cheking for paystack

        $payment = Payment::where('transaction_reference', $transaction_reference)->first();

        if(!is_null($payment))
        {
            $payment->transaction_status = 'failed';
            $payment->save();

            exit();
        }

        if($request->event == 'charge.success')
        {
            $payment_handler = new PaymentHandler();
            $payment_gateway_handler = $payment_handler->initializePaymentGateway($payment->gateway->name);
    
            $is_verified= $payment_gateway_handler->verify($request, $payment);
    
            $payment->refresh();
        }

        exit();
    }
}
