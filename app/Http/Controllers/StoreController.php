<?php

namespace App\Http\Controllers;

use App\Http\Resources\AddressResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\PromoResource;
use App\Http\Resources\StoreResource;
use App\Http\Requests\StorePromoRequest;
use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\StoreStoreRequest;
use App\Http\Requests\UpdateAddressRequest;
use App\Http\Requests\UpdatePromoRequest;
use App\Http\Requests\UpdateStoreRequest;
use App\Models\Store;

use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Http\Request;
class StoreController extends Controller
{

    /**
     * Create the controller instance.
     */
    public function __construct()
    {
        $this->authorizeResource(Store::class, 'store');
    }

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
        $store = Store::create($request->validated());
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
    public function productsIndex(Store $store)
    {
        $store_products = $store->products()->paginate();
        $product_resource = ProductResource::collection($store_products);
        $product_resource->with['message'] = 'Store products retrieved successfully';
        return $product_resource;
    }


    /**
     * Display a listing of the resource promos.
     * 
     * @return App\Http\Resources\PromoResource $promo_resource
     */
    public function promosIndex(Store $store, Request $request)
    {
        $promos = $store->inPromos()->paginate();
        $promo_resource = PromoResource::collection($promos);
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

    /**
     * Update new address for a store
     */
    public function updateAddress(UpdateAddressRequest $request)
    {
        $user = auth()->user();
        if(!$user->owns_a_store)
        {
            return response()->json([
                'message' => "This action is unauthorized.",
            ], 403);
        }

        $address = $user->store->address;

        $validated = $request->validated();
        $validated['first_name'] = array_key_exists('first_name', $validated) ? $validated['first_name'] : $address->first_name;
        $validated['last_name'] = array_key_exists('last_name', $validated) ? $validated['last_name'] : $address->last_name;
        $validated['dialing_code'] = array_key_exists('dialing_code', $validated) ? $validated['dialing_code'] : $address->dialing_code;
        $validated['phone_number'] = array_key_exists('phone_number', $validated) ? $validated['phone_number'] : $address->phone_number;

        $address->update($validated);
        

        $adresses_resource = new AddressResource($address);
        $adresses_resource->with['message'] = 'Store address updated successfully';

        return $adresses_resource;
    }

    /**
     * Display a specified store address
     */
    public function showAddress()
    {
        $user = auth()->user();

        if(!$user->owns_a_store)
        {
            return response()->json([
                'message' => "This action is unauthorized.",
            ], 403);
        }

        $adresses_resource = new AddressResource($user->store->address);
        $adresses_resource->with['message'] = 'Store address retrived successfully';

        return $adresses_resource;
    }

}
