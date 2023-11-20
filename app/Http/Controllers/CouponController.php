<?php

namespace App\Http\Controllers;

use App\Http\Resources\CouponResource;
use App\Http\Resources\ProductResource;
use App\Http\Requests\StoreCouponableRequest;
use App\Http\Requests\StoreCouponRequest;
use App\Http\Requests\UpdateCouponRequest;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Http\Request;
class CouponController extends Controller
{
    public function __construct(){
        $this->authorizeResource(Coupon::class, 'coupon');
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

        $coupons = Coupon::where('id', '<>', null);

        $coupons = is_null($order_by_code)? $coupons : $coupons->orderBy('code', $order_by_code ) ;
        $coupons = is_null($order_by_name)? $coupons : $coupons->orderBy('name', $order_by_name ) ;
        $coupons = is_null($order_by_created_at)? $coupons : $coupons->orderBy('name', $order_by_created_at ) ;

        $coupons = $coupons->paginate($per_page);

        $coupon_resource =  CouponResource::collection($coupons);
        $coupon_resource->with['status'] = "OK";
        $coupon_resource->with['message'] = 'Coupons retrieved successfully';

        return $coupon_resource;
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
    public function store(StoreCouponRequest $request)
    {
        $validated = $request->validated();

        $coupon = Coupon::create($validated);

        $coupon_resource = new CouponResource($coupon);
        $coupon_resource->with['message'] = 'Coupon created successfully';

        return $coupon_resource;
    }

    /**
     * Display the specified resource.
     */
    public function show(Coupon $coupon)
    {
        $coupons = new CouponResource($coupon);
        $coupons->with['message'] = "Coupon retrieved successfully.";

        return $coupons;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Coupon $coupon)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCouponRequest $request, Coupon $coupon)
    {
        $coupon->update($request->validated());
        $coupon_resource = new CouponResource($coupon);
        $coupon_resource->with['message'] = 'Coupon updated successfully';

        return $coupon_resource;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        $coupon_resource = new CouponResource(null);
        $coupon_resource->with['message'] = "Coupon deleted successfully";

        return $coupon_resource;
    }

    /**
     * Continue a specific coupon
     *
     * @param App\Models\Coupon $coupon
    */
    public function continue(Coupon $coupon){
        $coupon->is_cancelled = false;
        $coupon->save();

        $coupon_resource = new CouponResource($coupon);
        $coupon_resource->with['message'] = "Coupon continued successfully";

        return $coupon_resource;
    }

    /**
     * Cancel a specific running coupon
     *
     * @param App\Models\Coupon $coupon
     */

    public function cancel(Coupon $coupon){
        $coupon->is_cancelled = true;
        $coupon->save();

        $coupon_resource = new CouponResource($coupon);
        $coupon_resource->with['message'] = "Coupon cancelled successfully";

        return $coupon_resource;
    }

    /**
     * Get all products attached to the coupon
     *
     * @param App\Models\Coupon $coupon
     * @return App\Http\Resources\ProductResource $product_resource
     */
    public function productsIndex(Coupon $coupon)
    {
        $product_resource = new ProductResource($coupon->products);

        $product_resource->with['message'] = 'Coupon attached products retrieved successfully';
        return $product_resource;
    }

     /**
     * Attached products to coupon
     *
     * @param App\Models\Coupon $coupon
     * @param App\Http\Requests\StoreCouponableRequest $request
     * @return ProductResource $product_resource
     */
    public function attachProducts(Coupon $coupon, StoreCouponableRequest $request )
    {
        $validated = $request->validated();

        // attach by multiple product ids
       if(array_key_exists('product_ids', $validated))
       {
            foreach($validated['product_ids'] as $id){

                $product = Product::find($id);

                // check if the product is of the same store as the coupon
                if($product->store_id === $coupon->store_id)
                {
                    $coupon->products()->syncWithoutDetaching($product);
                }
            }
       }
        // attach by single product id
       elseif(array_key_exists('product_id', $validated))
       {
            $product = Product::find($validated['product_id']);

            // check if the product is of the same store as the coupon
            if($product->store_id === $coupon->store_id)
            {
                $coupon->products()->syncWithoutDetaching($product);
            }
       }


        $product_resource = new ProductResource($coupon->products);

        $product_resource->with['message'] = 'Product(s) attached to coupon succesfully';
        return $product_resource;
    }

    /**
     * Dettached products to coupon
     *
     * @param App\Models\Coupon $coupon
     * @param Illuminatie\Http\Request $request
     * @return App\Http\Resources\ProductResource $product_resource
     */
    public function detachProducts(Coupon $coupon, Request $request )
    {


        // detach by multiple product ids
       if($request->has('product_ids'))
       {
            $coupon->products()->detach($request->product_id);
       }
        // detach by single product id
       else if($request->has('product_id'))
       {
            $coupon->products()->detach($request->product_id);
       }


        $product_resource = new ProductResource($coupon->products);

        $product_resource->with['message'] = 'Product(s) detached from coupon succesfully';
        return $product_resource;
    }
}
