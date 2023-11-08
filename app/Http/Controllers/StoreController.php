<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Http\Resources\PromoResource;
use App\Http\Resources\StoreResource;
use App\Http\Requests\StorePromoRequest;
use App\Http\Requests\StoreStoreRequest;
use App\Http\Requests\UpdatePromoRequest;
use App\Http\Requests\UpdateStoreRequest;
use App\Models\Store;

use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Http\Request;
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
        $store = Store::create( $request->validated());
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

    /**
     * Display the specified store products.
     */
    public function productIndex(Store $store)
    {
        $store_products = $store->products()->paginate();
        $product_resource = new ProductResource($store_products);
        $product_resource->with['message'] = $store->name. '\'s products retrieved successfully';
        return $product_resource;
    }


    /**
     * Display a listing of the resource promos.
     * 
     * @return App\Http\Resources\PromoResource $promo_resource
     */
    public function promosIndex(Store $store, Request $request)
    {
        $promos = $store->promos;
        $promo_resource = new PromoResource($promos);
        $promo_resource->with['message'] = 'Store promos retrived successfully';
        return $promo_resource;
    }

    /**
     * Store a newly created resource promo in storage.
     * 
     * @param App\Http\Requests\StorePromoRequest $request
     * @return App\Http\Resources\PromoResource $product_resource
     */
    public function promosStore(Store $store, StorePromoRequest $request)
    {
        $validated = $request->validated();
        $promo = $store->promos()->create($validated);
        $promo_resource = new PromoResource($promo);
        $promo_resource->with['message'] = 'Store promo created successfully';
        return $promo_resource;
    }
}
