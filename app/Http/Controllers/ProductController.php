<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCouponableRequest;
use App\Http\Requests\StoreLikeRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\StorePromoableRequest;
use App\Http\Requests\StorePromoRequest;
use App\Http\Requests\UpdateLikeRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Requests\UpdatePromoRequest;
use App\Http\Resources\CouponResource;
use App\Http\Resources\LikeResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\PromoResource;
use App\Models\Coupon;
use App\Models\Image;
use App\Models\Product;
use App\Models\Promo;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return App\Http\Resources\ProductResource $product_resource
     */
    public function index(Request $request)
    {
        $per_page = is_numeric($request->per_page)? (int) $request->per_page : 15;

        $order_by_name = $request->order_by_name == 'asc' || $request->order_by_name == 'desc'
                        ? $request->order_by_name : null;

        $order_by_price = $request->order_by_price == 'asc' || $request->order_by_price == 'desc'
                        ? $request->order_by_price : null;

        $products = Product::where('id', '<>', null);

        $products = is_null($order_by_price)? $products : $products->orderBy('price', $order_by_price ) ;
        $products = is_null($order_by_name)? $products : $products->orderBy('name', $order_by_name ) ;

        $products = $products->paginate($per_page);

        $product_resource =  ProductResource::collection($products);
        $product_resource->with['status'] = "OK";
        $product_resource->with['message'] = 'Products retrived successfully';

        return $product_resource;
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
     *
     * @param App\Http\Requests\StoreProductRequest $request
     * @return App\Http\Resources\ProductResource $product_resource
     */
    public function store(StoreProductRequest $request)
    {
        $validated = $request->validated();

        if(auth()->check()){
            $user =  auth()->user();
            $validated['store_id'] = $user->ownsAStore ? $user->store->id : $validated['store_id'];
        }

        $product = Product::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'short_description' => $validated['short_description'] ?? null,
            'price' => $validated['price'],
            // 'discount' => $validated['discount'] ?? null,
            'category_id' => $validated['category_id'],
            'store_id' => $validated['store_id'],
        ]);

        // save product images
        if($request->hasFile('images'))
        {
            foreach ($request->file('images') as $image) {

                // $path = Storage::disk('public')->putFile('images', $image); // local storay

                $options = [
                    'overlayImageURL' => null, //
                    'thumbnail' => true, //true or false
                    'dimensions' => null, // null or ['width'=>700, 'height'=>700]
                    'roundCorners' => 0,
                ];

                // upload to cloudinary
                $uploaded_image = CloudinaryService::uploadImage($image, 'products/', $options);

                // save image information
                $image = new Image();
                // $image->url =  env('APP_URL').Storage::url($path);  // local storay
                $image->url = $uploaded_image->getSecurePath(); // cloudinary
                $image->imageable_id = $product->id;
                $image->imageable_type = $product::class;
                $image->save();
            }
        }
        // attach the product images
        $product->images;

        $product_resource = new ProductResource($product);
        $product_resource->with['message'] = 'Product created successfully';

        return $product_resource;
    }

    /**
     * Display the specified resource.
     *
     * @param App\Models\Product $product
     * @return App\Http\Resources\ProductResource $product_resource
     */
    public function show(Product $product)
    {
        $product_resource = new ProductResource($product);
        $product_resource->with['message'] = 'Product retrieved successfully';

        return  $product_resource;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param App\Models\Product $product
     * @return App\Http\Resources\ProductResource $product_resource
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update($request->validated());
        $product_resource = new ProductResource($product);
        $product_resource->with['message'] = 'Product updated successfully';

        return $product_resource;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param App\Models\Product $product
     * @return App\Http\Resources\ProductResource $product_resource
     */
    public function destroy(Product $product)
    {
        $product->delete();
        $product_resource = new ProductResource(null);
        $product_resource->with['message'] = 'Product deleted successfully';

        return $product_resource;
    }

    /**
     * Verify the specified resource.
     *
     * @param App\Models\Product $product
     * @return App\Http\Resources\ProductResource $product_resource
     */
    public function verify(Product $product)
    {
        $product->verified_at = now();
        $product->verifier_id = auth()->user()->id;
        $product->save();

        $product_resource = new ProductResource($product->only(['id', 'name','store_id','verified_at', 'verifier_id']));
        $product_resource->with['message'] = 'Product verified successfully';

        return $product_resource;
    }

    /**
     * Revoke verification of the specified resource.
     *
     * @param App\Models\Product $product
     * @return App\Http\Resources\ProductResource $product_resource
     */
    public function revokeVerification(Product $product)
    {
        $product->verified_at = null;
        $product->verifier_id = null;
        $product->save();

        $product_resource = new ProductResource($product->only(['id', 'name','store_id','verified_at', 'verifier_id']));
        $product_resource->with['message'] = 'Product verification revoked successfully';

        return $product_resource;
    }

    /**
     * Display a listing of the resource promos.
     *
     * @return App\Http\Resources\PromoResource $promo_resource
     */
    public function promosIndex(Product $product, Request $request)
    {
        $promos = $product->promos;
        $promo_resource = new PromoResource($promos);
        $promo_resource->with['message'] = 'Product promos retrived successfully';
        return $promo_resource;
    }

     /**
     * Store a newly created resource promo in storage.
     *
     * @param App\Http\Requests\StorePromoableRequest $request
     * @return App\Http\Resources\PromoResource $product_resource
     */
    public function promosStore(Product $product, StorePromoableRequest $request)
    {
        $validated = $request->validated();
        $promo = Promo::find($validated['promo_id']?? null);
        
        /**
         * Ensure the product does not have an active promo 
         */
        if(!is_null($product->activePromo)){

            $promo_resource = new PromoResource(null);
            $promo_resource->with['status'] = 'FAILED';
            $promo_resource->with['message'] = 'Promo attachment failed: active promo on the product.';

            return $promo_resource;
        }

        /**
         * Ensure the promo and product belongs to the same store
         */
        if($promo->store_id != $product->store_id){

            $promo_resource = new PromoResource(null);
            $promo_resource->with['status'] = 'FAILED';
            $promo_resource->with['message'] = 'Promo attachment failed: promo is of different stores';

            return $promo_resource;
        }
        
        // add the promo to product
        $product->promos()->syncWithoutDetaching($promo);
        $promo_resource = new PromoResource($promo);
        $promo_resource->with['message'] = 'Promo attached to product successfully';

        return $promo_resource;
      
    }

    /**
     * Display a listing of the resource coupons.
     *
     * @param App\Models\Product $product
     * @param Illuminatie\Http\Request $request
     * @return App\Http\Resources\CouponResource $coupon_resource
     */
    public function couponsIndex(Product $product, Request $request)
    {
        $coupons = $product->coupons;
        $coupon_resource = new CouponResource($coupons);
        $coupon_resource->with['message'] = 'Product coupons retrived successfully';
        return $coupon_resource;
    }
     /**
     * Store a newly created resource coupon in storage.
     *
     * @param App\Models\Product $product
     * @param App\Http\Requests\StoreCouponableRequest $request
     * @return App\Http\Resources\CouponResource $coupon_resource
     */
    public function couponsStore(Product $product, StoreCouponableRequest $request)
    {
        $validated = $request->validated();
        $coupon = Coupon::find($validated['coupon_id']?? null);
        
        /**
         * Ensure the product does not have an active coupon 
         */
        if(!is_null($product->activeCoupon)){

            $coupon_resource = new CouponResource(null);
            $coupon_resource->with['status'] = 'FAILED';
            $coupon_resource->with['message'] = 'Coupon attachment failed: active coupon on the product.';

            return $coupon_resource;
        }

        /**
         * Ensure the coupon and product belongs to the same store
         */
        if($coupon->store_id != $product->store_id){

            $coupon_resource = new CouponResource(null);
            $coupon_resource->with['status'] = 'FAILED';
            $coupon_resource->with['message'] = 'Coupon attachment failed: coupon is of different stores';

            return $coupon_resource;
        }
        
        // add the coupon to product
        $product->coupons()->syncWithoutDetaching($coupon);
        $coupon_resource = new CouponResource($coupon);
        $coupon_resource->with['message'] = 'Coupon attached to product successfully';

        return $coupon_resource;
      
    }

    /**
     * Display list of the specified product likes
     * 
     * @param Illuminate\Http\Request $request 
     * @param App\Models\Product $product
     */
    public function indexLikes(Request $request, Product $product)
    {
        $per_page = is_numeric($request->per_page)? (int) $request->per_page : 15;
        
        $product_likes = $product->likes()->paginate($per_page);

        $product_likes_resource =  LikeResource::collection($product_likes);
        $product_likes_resource->with['status'] = "OK";
        $product_likes_resource->with['message'] = $product->name."'s likes list - retrieved successfully";

        return $product_likes_resource;
    }

    /**
     * Like of the specified product
     * 
     * @param App\Http\Requests\UpdateLikeRequest $request 
     * @param App\Models\Product $product
     * @method POST
     */
    public function createLikes(StoreLikeRequest $request, Product $product)
    {
        $like = $product->likes()->firstOrCreate([
            'user_id' => auth()->user()->id
        ]);

        $like_resource = new LikeResource($like);
        $like_resource->with['status'] = "OK";
        $like_resource->with['message'] = "Product liked successfully";

        return $like_resource;
    }

    /**
     * undo like of the specified product
     * 
     * @param App\Http\Requests\UpdateLikeRequest $request 
     * @param App\Models\Product $product
     * @method DELETE
     */
    public function destroyLikes(UpdateLikeRequest $request, Product $product)
    {

        $product->likes()->where('user_id', auth()->user()->id)->first()->delete();
       
        $like_resource = new LikeResource(null);
        $like_resource->with['status'] = "OK";
        $like_resource->with['message'] = "Product like undo successfully";
    }

    /**
     * undo all likes of the specified product
     * 
     * @param App\Models\Product $product
     * @method DELETE
     */
    public function destroyAllLikes(Product $product){
        $product->likes()->delete();

        $like_resource = new LikeResource(null);
        $like_resource->with['status'] = "OK";
        $like_resource->with['message'] = "Product's likes undo successfully";
    }

}
