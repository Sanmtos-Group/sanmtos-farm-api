<?php

namespace App\Http\Controllers;

use App\Http\Resources\AddressResource;
use App\Models\Address;
use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\UpdateAddressRequest;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $addresses = Address::paginate();
        $address_resource = AddressResource::collection($addresses);
        
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
        $address = Address::create($request->validated());

        $addressResource = new AddressResource($address);
        $addressResource->with['message'] = "Address saved successfully";

        return $addressResource;
    }

    /**
     * Display the specified resource.
     */
    public function show(Address $address)
    {
        $addressResource =  new AddressResource($address);
        $addressResource->with['message']= 'Address retrieved successfully';

        return $addressResource;
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Address $address)
    {
        //
    }
}
