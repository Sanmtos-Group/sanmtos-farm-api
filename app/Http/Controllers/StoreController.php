<?php

namespace App\Http\Controllers;

use App\Http\Resources\StoreResource;
use App\Models\Store;
use App\Http\Requests\StoreStoreRequest;
use App\Http\Requests\UpdateStoreRequest;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stores = Store::all();
        $store_resource = new StoreResource($stores);
        return $store_resource;
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
    public function store(StoreStoreRequest $request)
    {
        $store = store::create($request->validated());
        $store_resource = new StoreResource($store);
        $store_resource->with['message'] = 'Store created successfully';

        return $store_resource;
    }

    /**
     * Display the specified resource.
     */
    public function show(Store $store)
    {
        $store_resource = new StoreResource($store);
        $store_resource->with['message'] = 'Store retrieved successfully';

        return $store_resource;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Store $store)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStoreRequest $request, Store $store)
    {
        $store->update($request->validated());
        $store_resource = new StoreResource($store);
        $store_resource->with['message'] = 'Store updated successfully';

        return $store_resource;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Store $store)
    {
        $store->delete();

        $store_resource = new StoreResource(null);
        $store_resource->with['message'] = 'Store deleted successfully';

        return $store_resource;
    }
}
