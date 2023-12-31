<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Promo;
use App\Http\Requests\StorePromoRequest;
use App\Http\Requests\StorePromoableRequest;
use App\Http\Requests\UpdatePromoRequest;
use App\Http\Resources\ProductResource;
use App\Http\Resources\PromoResource;
use Illuminate\Http\Request;
class PromoController extends Controller
{
    /**
     * Create the controller instance.
     */
    public function __construct()
    {
        $this->authorizeResource(Promo::class, 'promo');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $per_page = is_numeric($request->per_page)? (int) $request->per_page : 15;

        $order_by_code = $request->order_by_code == 'asc' || $request->order_by_code == 'desc'
        ? $request->order_by_code : null;

        $order_by_name = $request->order_by_name == 'asc' || $request->order_by_name == 'desc'
                        ? $request->order_by_name : null;

        $order_by_created_at = $request->order_by_created_at == 'asc' || $request->order_by_created_at == 'desc'
                        ? $request->order_by_created_at : null;
        
        $promos = Promo::where('id', '<>', null);

        $promos = is_null($order_by_code)? $promos : $promos->orderBy('code', $order_by_code ) ;
        $promos = is_null($order_by_name)? $promos : $promos->orderBy('name', $order_by_name ) ;
        $promos = is_null($order_by_created_at)? $promos : $promos->orderBy('name', $order_by_created_at ) ;

        $promos = $promos->paginate($per_page); 

        $promo_resource =  PromoResource::collection($promos);
        $promo_resource->with['status'] = "OK";
        $promo_resource->with['message'] = 'Promos retrived successfully';

        return $promo_resource;
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
    public function store(StorePromoRequest $request)
    {
        $validated = $request->validated();
        $promo = Promo::create($validated);

        $promo_resource = new PromoResource($promo);
        $promo_resource->with['message'] = 'Promo created successfully';

        return $promo_resource;
    }

    /**
     * Display the specified resource.
     */
    public function show(Promo $promo)
    {

        $promo_resource = new PromoResource($promo);
        $promo_resource->with['message'] = 'Promo retrived successfully';

        return $promo_resource;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Promo $promo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePromoRequest $request, Promo $promo)
    {
        $promo->update($request->validated());
        $promo_resource = new PromoResource($promo);
        $promo_resource->with['message'] = 'Promo updated successfully';

        return $promo_resource;
    }

    /**
     * Cancel the specified resource in storage.
     */
    public function cancel(Promo $promo)
    {
        $promo->is_cancelled = true;
        $promo->save();
        $promo_resource = new PromoResource($promo);
        $promo_resource->with['message'] = 'Promo cancelled successfully';

        return $promo_resource;
    }

    /**
     * Continue the specified resource in storage.
     */
    public function continue(Promo $promo)
    {
        $promo->is_cancelled = false;
        $promo->save();

        $promo_resource = new PromoResource($promo);
        $promo_resource->with['message'] = 'Promo continued successfully';

        return $promo_resource;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Promo $promo)
    {
        $promo->delete();
        $promo_resource = new PromoResource(null);
        $promo_resource->with['message'] = 'Promo deleted successfully';
        
        return $promo_resource;
    }

    /**
     * Get all products attached to the promo
     * 
     * @param App\Models\Promo $promo
     * @return App\Http\Resources\ProductResource $product_resource
     */
    public function productsIndex(Promo $promo)
    {
        $product_resource = new ProductResource($promo->products);

        $product_resource->with['message'] = 'Promo attached products retrieved succesfully';
        return $product_resource;
    }

    /**
     * Attached products to promo
     * 
     * @param App\Models\Promo $promo
     * @param App\Http\Requests\StorePromoableRequest $request
     * @return App\Http\Resources\ProductResource $product_resource
     */
    public function attachProducts(Promo $promo, StorePromoableRequest $request)
    {
        $validated = $request->validated();

        // attach by multiple product ids
       if(array_key_exists('product_ids', $validated))
       {
            foreach($validated['product_ids'] as $id){

                $product = Product::find($id);

                // check if the product is of the same store as the promo
                if($product->store_id === $promo->store_id)
                {
                    $promo->products()->syncWithoutDetaching($product); 
                }  
            }     
       }
        // attach by single product id
       elseif(array_key_exists('product_id', $validated))
       {
            $product = Product::find($validated['product_id']);

            // check if the product is of the same store as the promo
            if($product->store_id === $promo->store_id)
            {
                $promo->products()->syncWithoutDetaching($product); 
            }      
       }


        $product_resource = new ProductResource($promo->products);

        $product_resource->with['message'] = 'Product(s) attached to promo succesfully';
        return $product_resource;
    }

    /**
     * Dettached products to promo
     * 
     * @param App\Models\Promo $promo
     * @param Illuminatie\Http\Request $request
     * @return App\Http\Resources\ProductResource $product_resource
     */
    public function detachProducts(Promo $promo, Request $request )
    {
      

        // detach by multiple product ids
       if($request->has('product_ids'))
       {
            $promo->products()->detach($request->product_id); 
       }
        // detach by single product id
       else if($request->has('product_id'))
       {
            $promo->products()->detach($request->product_id);   
       }


        $product_resource = new ProductResource($promo->products);

        $product_resource->with['message'] = 'Product(s) detached from promo succesfully';
        return $product_resource;
    }
}
