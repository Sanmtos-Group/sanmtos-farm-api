<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\StorePromoRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Requests\UpdatePromoRequest;
use App\Http\Resources\ProductResource;
use App\Http\Resources\PromoResource;
use App\Models\Image;
use App\Models\Product;

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

        $products = $products->paginate();

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

            foreach ($request->file('images') as $image) {
                $path = Storage::disk('public')->putFile('images', $image);
                $image = new Image();
                $image->url =  env('APP_URL').Storage::url($path);
                $image->imageable_id = $product->id;
                $image->imageable_type = $product::class;
                $image->save();
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
     * @return App\Http\Resourcetaw$product_resource
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
     * @param App\Http\Requests\StorePromoRequest $request
     * @return App\Http\Resources\PromoResource $product_resource
     */
    public function promosStore(Product $product, StorePromoRequest $request)
    {
        $validated = $request->validated();
        $promo = $product->promos()->create($validated);
        $promo_resource = new PromoResource($promo);
        $promo_resource->with['message'] = 'Product promo created successfully';
        return $promo_resource;
    }

}
