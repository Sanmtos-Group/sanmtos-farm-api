<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Promo;
use App\Models\Image;
use App\Http\Requests\StorePromoRequest;
use App\Http\Requests\StorePromoableRequest;
use App\Http\Requests\UpdatePromoRequest;
use App\Http\Resources\ProductResource;
use App\Http\Resources\PromoResource;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;
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
        $user = auth()->user();

        $promos = QueryBuilder::for(Promo::class)
        ->defaultSort('created_at')
        ->allowedSorts(
            'name',
            'description',
            'discount',
            'start_datetime',
            'end_datetime',
            'is_unlimited',
            'created_at',
            'updated_at',
            AllowedSort::custom('recent', new \App\Models\Sorts\LatestSort()),
            AllowedSort::custom('oldest', new \App\Models\Sorts\OldestSort()),
        )
        ->allowedFilters([
            'name',
            'description',
            'discount',
            'discount_type_id',
            'free_delivery',
            'free_advert',
            'start_datetime',
            'end_datetime',
            'is_unlimited',
            'store_id',
            'created_at',
            'updated_at',
            AllowedFilter::exact('store_id'),
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

        $promo_resource =  PromoResource::collection($promos);
        $promo_resource->with['status'] = "OK";
        $promo_resource->with['message'] = 'Promos retrieved successfully';

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
        $user = auth()->user();

        $validated = $request->validated();
        $promo = Promo::create($validated);

        if(!is_null($user) && $user->owns_a_store)
        {
            $promo->store_id = $user->store->id;
            $promo->save();
        }
      
        $recipients = [];
        // clean recipient ids for syncing 
        foreach ($validated['recipient_ids']?? [] as $key=>$user_id) 
        {
            $recipients [$user_id] = [
                // Other pivot table attributes if needed
                'id' => Str::uuid()->toString(), // Generate UUID for the pivot ID
            ];
        }
        $promo->recipients()->syncWithoutDetaching($recipients);
        $promo->recipients;  
        
        $applicable_products = [];
        // clean applicable product ids for syncing 
        foreach ($validated['applicable_product_ids']?? [] as $key=>$product_id) 
        {
            $applicable_products [$product_id] = [
                // Other pivot table attributes if needed
                'id' => Str::uuid()->toString(), // Generate UUID for the pivot ID
            ];
        }
        $promo->applicableProducts()->syncWithoutDetaching($applicable_products);
        $promo->applicableProducts; 

        $applicable_categories = [];
        // clean applicable category ids for syncing 
        foreach ($validated['applicable_category_ids']?? [] as $key=>$category_id) 
        {
            $applicable_categories [$category_id] = [
                // Other pivot table attributes if needed
                'id' => Str::uuid()->toString(), // Generate UUID for the pivot ID
            ];
        }
        $promo->applicableCategories()->syncWithoutDetaching($applicable_categories);
        $promo->applicableCategories; 

        // save promo image
        if($request->hasFile('image'))
        {
            $image=$request->file('image');

            // $path = Storage::disk('public')->putFile('images', $image); // local storage

            $options = [
                'overlayImageURL' => null, //
                'thumbnail' => true, //true or false
                'dimensions' => null, // null or ['width'=>700, 'height'=>700]
                'roundCorners' => 0,
            ];

            // upload to cloudinary
            $uploaded_image = CloudinaryService::uploadImage($image, 'promos/', $options);

            // save image information
            $image = new Image();
            // $image->url =  env('APP_URL').Storage::url($path);  // local storage
            $image->url = $uploaded_image->getSecurePath(); // cloudinary
            $image->imageable_id = $promo->id;
            $image->imageable_type = $promo::class;
            $image->save();
            
        }
        // attach the promo image to response
        $promo->image;

        $promo_resource = new PromoResource($promo);
        $promo_resource->with['message'] = 'Promo created successfully';

        return $promo_resource;
    }

   /**
     * Display the specified resource.
     */
    public function show(Promo $promo)
    {
        if(request()->has('include'))
        {
            foreach (explode(',', request()->include) as $key => $value) {
               $promo->{$value};
            }
        }

        $promo_resource = new PromoResource($promo);
        $promo_resource->with['message'] = "Promo retrieved successfully.";

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
        $coupon->cancellation_reason = null;
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
     * Add to promo recipients
     *
     * @param App\Models\Promo $promo
     * @param App\Http\Requests\StorePromoableRequest $request
     * @return ProductResource $product_resource
     */
    public function attachRecipients(Promo $promo, StorePromoableRequest $request )
    {
        $validated = $request->validated();

        // attach by multiple product ids
       if(array_key_exists('recipient_ids', $validated))
       {
            $recipient_users = [];
            // clean applicable product ids for syncing 
            foreach ($validated['recipient_ids']?? [] as $key=>$user_id) 
            {
                $recipient_users [$user_id] = [
                    // Other pivot table attributes if needed
                    'id' => Str::uuid()->toString(), // Generate UUID for the pivot ID
                ];
            }

            $promo->recipients()->syncWithoutDetaching($recipient_users);
            $promo->recipients; 
       }
        // attach by single product id
       elseif(array_key_exists('recipient_id', $validated))
       {
            $promo->recipients()->syncWithoutDetaching([
                $validated['recipient_id'] => [
                    // Other pivot table attributes if needed
                    'id' => Str::uuid()->toString(), // Generate UUID for the pivot ID
                ]
            ]);

            
       }

        $promo->recipients; 
        
        $promo_resource = new PromoResource($promo);
        $promo_resource->with['message'] = 'Recipients(s) attached to promo succesfully';
        return $promo_resource;
    }

     /**
     * Detached recipients from promo
     *
     * @param App\Models\Promo $promo
     * @param Illuminatie\Http\Request $request
     * @return App\Http\Resources\ProductResource $product_resource
     */
    public function detachRecipients(Promo $promo, Request $request )
    {
        // detach by multiple product ids
        if($request->has('recipient_ids') || $request->has('recipient_id'))
        {
            $promo->recipients()->detach($request->recipient_ids);
            $promo->recipients()->detach($request->recipient_id);
        }
         
        $promo->recipients; 
        
        $promo_resource = new PromoResource($promo);
        $promo_resource->with['message'] = 'Recipient(s) detached from promo succesfully';
        return $promo_resource;
    }

     /**
     * Add to promo applicable products
     *
     * @param App\Models\Promo $promo
     * @param App\Http\Requests\StorePromoableRequest $request
     * @return ProductResource $product_resource
     */
    public function attachApplicableProducts(Promo $promo, StorePromoableRequest $request )
    {
        $validated = $request->validated();

        // attach by multiple product ids
       if(array_key_exists('product_ids', $validated))
       {
            
            $product_ids = $promo->store->products()->whereIn('id', $validated['product_ids'])->pluck('id');

            $applicable_products = [];
            // clean applicable product ids for syncing 
            foreach ($product_ids?? [] as $key=>$product_id) 
            {
                $applicable_products [$product_id] = [
                    // Other pivot table attributes if needed
                    'id' => Str::uuid()->toString(), // Generate UUID for the pivot ID
                ];
            }

            $promo->applicableProducts()->syncWithoutDetaching($applicable_products);
            $promo->applicableProducts; 
       }
        // attach by single product id
       elseif(array_key_exists('product_id', $validated))
       {
            $product_id = $promo->store->products()->where('id', $validated['product_id'])->pluck('id')->first();

            $promo->applicableProducts()->syncWithoutDetaching([
                $product_id => [
                    // Other pivot table attributes if needed
                    'id' => Str::uuid()->toString(), // Generate UUID for the pivot ID
                ]
            ]);

            
       }

        $promo->applicableProducts; 
        
        $promo_resource = new PromoResource($promo);
        $promo_resource->with['message'] = 'Product(s) attached to promo succesfully';
        return $promo_resource;
    }

    /**
     * Detached products to promo
     *
     * @param App\Models\Promo $promo
     * @param Illuminatie\Http\Request $request
     * @return App\Http\Resources\ProductResource $product_resource
     */
    public function detachApplicableProducts(Promo $promo, Request $request )
    {
        // detach by multiple product ids
        if($request->has('product_ids'))
        {
            $promo->applicableProducts()->detach($request->product_ids);
        }
            // detach by single product id
        else if($request->has('product_id'))
        {
            $promo->applicableProducts()->detach($request->product_id);
        }

        $promo->applicableProducts; 
        
        $promo_resource = new PromoResource($promo);
        $promo_resource->with['message'] = 'Product(s) detached from promo succesfully';
        return $promo_resource;
    }


    /**
     * Add to promo applicable categories
     *
     * @param App\Models\Promo $promo
     * @param App\Http\Requests\StorePromoableRequest $request
     * @return PromoResource $promo_resource
     */
    public function attachApplicableCategories(Promo $promo, StorePromoableRequest $request )
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

            $promo->applicableCategories()->syncWithoutDetaching($applicable_categories);
            $promo->applicableCategories; 
       }
        // attach by single category id
       elseif(array_key_exists('category_id', $validated))
       {
            $promo->applicableCategories()->syncWithoutDetaching([
                $validated['category_id'] => [
                    // Other pivot table attributes if needed
                    'id' => Str::uuid()->toString(), // Generate UUID for the pivot ID
                ]
            ]);
            
       }

        $promo->applicableCategories; 
        
        $promo_resource = new PromoResource($promo);
        $promo_resource->with['message'] = 'Category(ies) attached to promo succesfully';
        return $promo_resource;
    }

    /**
     * Detached categories to promo
     *
     * @param App\Models\Promo $promo
     * @param Illuminatie\Http\Request $request
     * @return App\Http\Resources\ProductResource $product_resource
     */
    public function detachApplicableCategories(Promo $promo, Request $request )
    {
        // detach by multiple category ids
        if($request->has('category_ids'))
        {
            $promo->applicableCategories()->detach($request->category_ids);
        }
        // detach by single category id
        else if($request->has('category_id'))
        {
            $promo->applicableCategories()->detach($request->category_id);
        }

        $promo->applicableCategories; 
        
        $promo_resource = new PromoResource($promo);
        $promo_resource->with['message'] = 'Category(ies) detached from promo succesfully';
        return $promo_resource;
    }
}
