<?php

namespace App\Http\Controllers;

use App\Http\Resources\PaymentResource;
use App\Models\Order;
use App\Models\Payment;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Models\User;
use Illuminate\Support\Facades\Redirect;
use Unicodeveloper\Paystack\Paystack;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        //
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

    /**
     * Redirect the User to Paystack Payment Page
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function makePayment(StorePaymentRequest $request)
    {
        $requests = $request->validated();
        $code = paystack()->getPaymentData();; #Generate a payment reference code
//        return $code;
        foreach ($requests as $data){ #Save data into database table for secure payment
            $payment = new Payment();

            $payment->amount = $data->amount;
            $payment->user_id = $data->user_id;
            $payment->payment_gate_way = "paystack";
            $payment->payment_type = $data->payment_type;
            $payment->product_id = $data->product_id;
            $payment->transaction_reference = $code;

            $payment->save();
        }

        $data = array( #Send the required data to the payment gateway
            "amount" => $requests->amount,
            "email" => $requests->email,
            "reference" => $code, #"12345" I used this integers maybe the error will be solved but not
            "orderID" => $requests->product_id

        );

        return paystack()->getAuthorizationUrl($data)->redirectNow();
    }

    /**
     * Obtain Paystack payment information
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleGatewayCallback()
    {
        $paymentDetails = paystack()->getPaymentData();
//        dd($paymentDetails);
        $user = User::where('email', $paymentDetails->email)->first(); #Chech if the email is in database
        $payment = Payment::where('transaction_reference', $paymentDetails->reference, 'product_id', $paymentDetails->orderID);

        if ($user || $payment) {
            #Update the Payment table if verification passed
            Payment::update([
                "payment_method" => $paymentDetails->payment_method,
                "transaction_status" => "successful"
            ]);

//            Order::create([]); #Am trying to store order after successful payment

            return response()->json([
                "message" => "Payment successful, we will send your order tracking ID to your email.",
                "data" => $user
            ], 200);
        }else{
            return response()->json([
                "message" => "Something went wrong",
                "data" => []
            ], 501);
        }
    }

    /**
     * This method gets all the transactions that have occurred
     * @returns array
     */

    public function getAllTransactions()
    {
        $transactions = paystack()->getAllTransactions();

        $resource = new PaymentResource($transactions);
        $resource->with['message'] = 'Transactions retrieved successfully';

        return $resource;
    }

    /**
     * This method gets all the customers that have performed transactions on your platform with Paystack
     * @returns array
     */

    public function getAllCustomersTransacted()
    {
        $customer = paystack()->getAllCustomers();

        $resource = new PaymentResource($customer);
        $resource->with['message'] = 'Transacted customers retrieved successfully';

        return $resource;
    }
}
