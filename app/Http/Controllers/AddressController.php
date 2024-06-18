<?php

namespace App\Http\Controllers;

use App\Http\Resources\AddressResource;
use App\Models\Address;
use App\Models\User;
use App\Models\Store;
use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\UpdateAddressRequest;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class AddressController extends Controller
{

    /**
     * Create the controller instance.
     */
    public function __construct()
    {
        $this->authorizeResource(Address::class, 'address');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $addresses = QueryBuilder::for(Address::class)
        ->defaultSort('-created_at')
        ->allowedSorts(
            'price',
            'total_price',
            'status',
            AllowedSort::custom('recent', new \App\Models\Sorts\LatestSort()),
            AllowedSort::custom('oldest', new \App\Models\Sorts\OldestSort()),
        )
        ->allowedFilters([
            AllowedFilter::exact('addressable_id'),
            AllowedFilter::exact('addressable_type'),
        ])
        ->allowedIncludes([
            'addressable',
            'country',
        ])
        ->paginate()
        ->appends(request()->query());

        $address_resource =  AddressResource::collection($addresses);

        $address_resource->with['status'] = "OK";
        $address_resource->with['message'] = 'Addresses retrived successfully';

        return $address_resource;
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
    public function store(StoreAddressRequest $request)
    {
        $validated = $request->validated();
        $address = null;

        if($validated['addressable_type'] == User::class)
        {
            $user = User::find($validated['addressable_id']);
            $validated['first_name'] = array_key_exists('first_name', $validated) ? $validated['first_name'] : $user->first_name ?? null;
            $validated['last_name'] = array_key_exists('last_name', $validated) ? $validated['last_name'] : $user->last_name ?? null;
            $validated['dialing_code'] = array_key_exists('dialing_code', $validated) ? $validated['dialing_code'] : $user->dialing_code ?? null;
            $validated['phone_number'] = array_key_exists('phone_number', $validated) ? $validated['phone_number'] : $user->phone_number ?? null;
            $address = Address::create($validated);
        }

        if($validated['addressable_type'] == Store::class)
        {
            $store = Store::find($validated['addressable_id']);
            $validated['first_name'] = array_key_exists('first_name', $validated) ? $validated['first_name'] : $store->name;
            $validated['last_name'] = array_key_exists('last_name', $validated) ? $validated['last_name'] : $store->name;
            $validated['dialing_code'] = array_key_exists('dialing_code', $validated) ? $validated['dialing_code'] : $store->dialing_code;
            $validated['phone_number'] = array_key_exists('phone_number', $validated) ? $validated['phone_number'] : $store->phone_number;

            if(is_null($address=$store->address))
            {
                $address = Address::create($validated);
            }
            else 
            {
                $address->update($validated);
            }

        }

        
        $address_resource = new AddressResource($address);
        $address_resource->with['message'] = "Address created successfully";

        return $address_resource;
    }

    /**
     * Display the specified resource.
     */
    public function show(Address $address)
    {
        $address_resource =  new AddressResource($address);
        $address_resource->with['message']= 'Address retrieved successfully';

        return $address_resource;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Address $address)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAddressRequest $request, Address $address)
    {
        $validated = $request->validated();

        if($address->addressable_type == User::class)
        {
            $validated['first_name'] = array_key_exists('first_name', $validated) ? $validated['first_name'] : $address->first_name;
            $validated['last_name'] = array_key_exists('last_name', $validated) ? $validated['last_name'] : $address->last_name;
            $validated['dialing_code'] = array_key_exists('dialing_code', $validated) ? $validated['dialing_code'] : $address->dialing_code;
            $validated['phone_number'] = array_key_exists('phone_number', $validated) ? $validated['phone_number'] : $address->phone_number;
        }

        $address->update($validated);
        $address_resource = new AddressResource($address);
        $address_resource->with['message'] = "Address updated successfully";

        return $address_resource;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Address $address)
    {
        $address->delete();

        $adresses_resource = new AddressResource(null);
        $adresses_resource->with['message'] = 'Address deleted successfully';
        return $adresses_resource;
    }
}
