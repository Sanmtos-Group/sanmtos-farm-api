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
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;
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

        $coupons = QueryBuilder::for(Coupon::class)
        ->defaultSort('created_at')
        ->allowedSorts(
            'code',
            'discount',
            'description',
            'discount_type_id',
            'discount',
            'created_at',
            'updated_at',
            AllowedSort::custom('recent', new \App\Models\Sorts\LatestSort()),
            AllowedSort::custom('oldest', new \App\Models\Sorts\OldestSort()),
        )
        ->allowedFilters([
            'code',
            'discount',
            'description',
            'discount_type_id',
            'discount',
            'requires_min_purchase',
            'min_purchase_price',
            'is_for_first_purchase_only',
            'max_usage',
            'unlimited_usage',
            'expiration_date',
            'cancelled_at',
            'store_id',
            'created_at',
            'updated_at',
            AllowedFilter::scope('store'),
            AllowedFilter::scope('discount_types'),
            AllowedFilter::scope('recipients'),
            AllowedFilter::scope('applicable_products'),
            AllowedFilter::scope('applicable_categories'),
        ])
        ->allowedIncludes([
            'store',
            'recipients',
            'discountType',
            'applicableProducts',
            'applicableCategories',
            'usages'
        ])
        ->paginate()
        ->appends(request()->query());

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

      
        $recipients = [];
        // clean recipient ids for syncing 
        foreach ($validated['recipient_ids']?? [] as $key=>$user_id) 
        {
            $recipients [$user_id] = [
                // Other pivot table attributes if needed
                'id' => Str::uuid()->toString(), // Generate UUID for the pivot ID
            ];
        }
        $coupon->recipients()->syncWithoutDetaching($recipients);
        $coupon->recipients;  
        
        $applicable_products = [];
        // clean applicable product ids for syncing 
        foreach ($validated['applicable_product_ids']?? [] as $key=>$product_id) 
        {
            $applicable_products [$product_id] = [
                // Other pivot table attributes if needed
                'id' => Str::uuid()->toString(), // Generate UUID for the pivot ID
            ];
        }
        $coupon->applicableProducts()->syncWithoutDetaching($applicable_products);
        $coupon->applicableProducts; 

        $applicable_categories = [];
        // clean applicable category ids for syncing 
        foreach ($validated['applicable_category_ids']?? [] as $key=>$category_id) 
        {
            $applicable_categories [$category_id] = [
                // Other pivot table attributes if needed
                'id' => Str::uuid()->toString(), // Generate UUID for the pivot ID
            ];
        }
        $coupon->applicableCategories()->syncWithoutDetaching($applicable_categories);
        $coupon->applicableCategories; 

        $coupon_resource = new CouponResource($coupon);
        $coupon_resource->with['message'] = 'Coupon created successfully';

        return $coupon_resource;
    }

    /**
     * Display the specified resource.
     */
    public function show(Coupon $coupon)
    {
        if(request()->has('include'))
        {
            foreach (explode(',', request()->include) as $key => $value) {
               $coupon->{$value};
            }
        }

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
        $coupon->cancelled_at = null;
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
        $coupon->cancelled_at = now();
        $coupon->save();

        $coupon_resource = new CouponResource($coupon);
        $coupon_resource->with['message'] = "Coupon cancelled successfully";

        return $coupon_resource;
    }

     /**
     * Add to coupon applicable products
     *
     * @param App\Models\Coupon $coupon
     * @param App\Http\Requests\StoreCouponableRequest $request
     * @return ProductResource $product_resource
     */
    public function attachApplicableProducts(Coupon $coupon, StoreCouponableRequest $request )
    {
        $validated = $request->validated();

        // attach by multiple product ids
       if(array_key_exists('product_ids', $validated))
       {
            
            $product_ids = $coupon->store->products()->whereIn('id', $validated['product_ids'])->pluck('id');

            $applicable_products = [];
            // clean applicable product ids for syncing 
            foreach ($product_ids?? [] as $key=>$product_id) 
            {
                $applicable_products [$product_id] = [
                    // Other pivot table attributes if needed
                    'id' => Str::uuid()->toString(), // Generate UUID for the pivot ID
                ];
            }

            $coupon->applicableProducts()->syncWithoutDetaching($applicable_products);
            $coupon->applicableProducts; 
       }
        // attach by single product id
       elseif(array_key_exists('product_id', $validated))
       {
            $product_id = $coupon->store->products()->where('id', $validated['product_id'])->pluck('id')->first();

            $coupon->applicableProducts()->syncWithoutDetaching([
                $product_id => [
                    // Other pivot table attributes if needed
                    'id' => Str::uuid()->toString(), // Generate UUID for the pivot ID
                ]
            ]);

            
       }

        $coupon->applicableProducts; 
        
        $coupon_resource = new CouponResource($coupon);
        $coupon_resource->with['message'] = 'Product(s) attached to coupon succesfully';
        return $coupon_resource;
    }

    /**
     * Dettached products to coupon
     *
     * @param App\Models\Coupon $coupon
     * @param Illuminatie\Http\Request $request
     * @return App\Http\Resources\ProductResource $product_resource
     */
    public function detachApplicableProducts(Coupon $coupon, Request $request )
    {
        // detach by multiple product ids
        if($request->has('product_ids'))
        {
            $coupon->applicableProducts()->detach($request->product_ids);
        }
            // detach by single product id
        else if($request->has('product_id'))
        {
            $coupon->applicableProducts()->detach($request->product_id);
        }

        $coupon->applicableProducts; 
        
        $coupon_resource = new CouponResource($coupon);
        $coupon_resource->with['message'] = 'Product(s) detached from coupon succesfully';
        return $coupon_resource;
    }


    /**
     * Add to coupon applicable categories
     *
     * @param App\Models\Coupon $coupon
     * @param App\Http\Requests\StoreCouponableRequest $request
     * @return CouponResource $coupon_resource
     */
    public function attachApplicableCategories(Coupon $coupon, StoreCouponableRequest $request )
    {
        $validated = $request->validated();

        // attach by multiple category ids
       if(array_key_exists('category_ids', $validated))
       {
            $applicable_categories = [];
            // clean applicable category ids for syncing 
            foreach ($validated['category_ids']?? [] as $key=>$category_id) 
            {
                $applicable_categories [$category_id] = [
                    // Other pivot table attributes if needed
                    'id' => Str::uuid()->toString(), // Generate UUID for the pivot ID
                ];
            }

            $coupon->applicableCategories()->syncWithoutDetaching($applicable_categories);
            $coupon->applicableCategories; 
       }
        // attach by single category id
       elseif(array_key_exists('category_id', $validated))
       {
            $coupon->applicableCategories()->syncWithoutDetaching([
                $validated['category_id'] => [
                    // Other pivot table attributes if needed
                    'id' => Str::uuid()->toString(), // Generate UUID for the pivot ID
                ]
            ]);
            
       }

        $coupon->applicableCategories; 
        
        $coupon_resource = new CouponResource($coupon);
        $coupon_resource->with['message'] = 'Category(ies) attached to coupon succesfully';
        return $coupon_resource;
    }

    /**
     * Dettached categories to coupon
     *
     * @param App\Models\Coupon $coupon
     * @param Illuminatie\Http\Request $request
     * @return App\Http\Resources\ProductResource $product_resource
     */
    public function detachApplicableCategories(Coupon $coupon, Request $request )
    {
        // detach by multiple category ids
        if($request->has('category_ids'))
        {
            $coupon->applicableCategories()->detach($request->category_ids);
        }
            // detach by single category id
        else if($request->has('category_id'))
        {
            $coupon->applicableCategories()->detach($request->category_id);
        }

        $coupon->applicableCategories; 
        
        $coupon_resource = new CouponResource($coupon);
        $coupon_resource->with['message'] = 'Category(ies) detached from coupon succesfully';
        return $coupon_resource;
    }
}
