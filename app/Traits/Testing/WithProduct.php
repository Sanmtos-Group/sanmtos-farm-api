<?php
namespace App\Traits\Testing; 
use App\Models\Product;

trait WithProduct {

    /**
     * The product instance.
     *
     * @var \App\Models\Product
     */
    protected $product;

    /**
     * Setup up a new product instance.
     *
     * @return \App\Models\Product
     */
    protected function setUpProduct(): void
    {
        $this->product = Product::factory()->create();
    }

    /**
     * @return \App\Models\Product
     */
    protected function makeProduct($product_data = null ): Product
    {
        return is_array($product_data) ? Product::factory()->make($product_data) : Product::factory()->make() ;   
    }

     /**
     * Get the product instance for a given data.
     *
     * @param  array<string ,*>|null  $product_data
     * 
     * @return \App\Models\Product
     */
    public function product($product_data = null ): Product
    {
        $product = is_array($product_data) ? Product::firstOrCreate(Product::factory()->make($product_data)->toArray()) : Product::first();
        return $product ?? Product::factory()->create();
    }

    /**
     * Get a trashed product data.
     *
     * @return \App\Models\Product
     */
    public function ProductTrashed(): Product 
    {
        $product_trashed = Product::onlyTrashed()->get()->first();
        if($product_trashed)
            return  $product_trashed;
            
        $product_trashed = $this->product();
        $product_trashed->delete();
        return $product_trashed;
    }

}