<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::all();
        $product_resource = new ProductResource($products);
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
     */
    public function store(StoreProductRequest $request)
    {
        $product = Product::create($request->validated());
        $product_resource = new ProductResource($product);
        $product_resource->with['message'] = 'Product created successfully';

        return $product_resource;
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product_resource = new ProductResource($product);
        $product_resource->with['message'] = 'Product retrieved successfully';

        return $product_resource;
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
     */
    public function verify(Product $product)
    {
        $product->verified_at = now();
        $product->verifier_id = auth()->user()->id;
        $product->save();

        $product_resource = new ProductResource($product);
        $product_resource->with['message'] = 'Product verified successfully';
        
        return $product_resource;
    }

     /**
     * Revoke verification of the specified resource.
     */
    public function revokeVerification(Product $product)
    {
        $product->verified_at = null;
        $product->verifier_id = null;
        $product->save();

        $product_resource = new ProductResource($product);
        $product_resource->with['message'] = 'Product verification revoked successfully';
        
        return $product_resource;
    }
}
