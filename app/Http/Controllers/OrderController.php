<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class OrderController extends Controller
{
     /**
     * Create the controller instance.
     */
    public function __construct()
    {
        $this->authorizeResource(Order::class, 'order');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = QueryBuilder::for(Order::class)
        ->defaultSort('-created_at')
        ->allowedSorts(
            'price',
            'total_price',
            'status',
            AllowedSort::custom('recent', new \App\Models\Sorts\LatestSort()),
            AllowedSort::custom('oldest', new \App\Models\Sorts\OldestSort()),
        )
        ->allowedFilters([
            AllowedFilter::exact('user_id'),
            AllowedFilter::exact('status'),
            AllowedFilter::exact('is_paid'),
            AllowedFilter::exact('ordered_at')->ignore(null),
        ])
        ->allowedIncludes([
            'user',
            'payment',
            'orderables',
        ])
        ->paginate()
        ->appends(request()->query());

        $order_resource =  OrderResource::collection($orders);

        $order_resource->with['status'] = "OK";
        $order_resource->with['message'] = 'Orders retrived successfully';

        return $order_resource;
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
    public function store(StoreOrderRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        $order->load('orderables', 'payment');
        
        if(request()->has('include'))
        {
            foreach (explode(',', request()->include) as $key => $value) {
               $order->{$value};
            }
        }

        $order_resource = new OrderResource($order);
        $order_resource->with['message'] = 'Order retrieved successfully';

        return  $order_resource;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        $validated = $request->validated();

        if(array_key_exists('alternative_phone_numbers', $validated))
        {
            $metadata =  $order->metadata;
            $metadata['alternative_phone_numbers'] = \explode(',', $validated['alternative_phone_numbers']);
            $order->metadata = $metadata ;
            $order->save();
        }

        $order_resource = new OrderResource($order);
        $order_resource->with['message'] = 'Order updated successfully';

        return $order_resource;

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }
}
