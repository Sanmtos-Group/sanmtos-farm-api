<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentGatewayRequest;
use App\Http\Requests\UpdatePaymentGatewayRequest;
use App\Http\Resources\PaymentGatewayResource;
use App\Models\PaymentGateway;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class PaymentGatewayController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * @return App\Http\Resources\PaymentGatewayResource $payment_gateway_resource
     */
    public function index()
    {
        $payment_gateways = QueryBuilder::for(PaymentGateway::class)
        ->defaultSort('is_default')
        ->allowedSorts(
            'name',
            'is_active',
            'is_default', 
            'created_at',
            AllowedSort::custom('recent', new \App\Models\Sorts\LatestSort()),
            AllowedSort::custom('oldest', new \App\Models\Sorts\OldestSort()),
        )
        ->allowedFilters([
            'name',
            'is_active',
            'is_default', 
            'created_at',
        ])
        ->allowedIncludes([
            'image',
        ])
        ->paginate()
        ->appends(request()->query());

        $payment_gateway_resource =  PaymentGatewayResource::collection($payment_gateways);

        $payment_gateway_resource->with['status'] = "OK";
        $payment_gateway_resource->with['message'] = 'Payment gateways retrived successfully';

        return $payment_gateway_resource;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePaymentGatewayRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     * 
     * @param App\Models\PaymentGateway $payment_gateway
     * @return App\Http\Resources\PaymentGatewayResource $payment_gateway_resource
     */
    public function show(PaymentGateway $paymentGateway)
    {
        $payment_gateway_resource = new PaymentGatewayResource($paymentGateway);
        $payment_gateway_resource->with['message'] = 'Payment gateway retrieved successfully';

        return  $payment_gateway_resource;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PaymentGateway $paymentGateway)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePaymentGatewayRequest $request, PaymentGateway $paymentGateway)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaymentGateway $paymentGateway)
    {
        //
    }
}
