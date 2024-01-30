<?php
namespace App\Traits; 

use App\Facades\CartFacade;
use App\Models\Cart;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasCartable {

    /**
     * Get all of the prodcut's cart in.
     */
    public function carts(): MorphMany
    {
        return $this->morphMany(Cart::class, 'cartable');
    }


    /**
     * Show the product is in cart
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function isInCart(): Attribute
    {

        if(auth()->user())
        {
            $is_in_cart = is_null($this->carts()->where('user_id', auth()->user()->id)->first())? false : true;
        }
        else{
            $is_in_cart = is_null(CartFacade::find($this->id)) ? false : true;
        }

        return Attribute::make(
            get: fn ($value) => $is_in_cart
        );
    }

    /**
     * Boot the HasReviews trait for a model.
     *
     * This method is automatically called when the model is booted.
     * It hooks into the "retrieved" event, which is fired when a model
     * instance is retrieved from the database. In this method, we
     * dynamically append the specified attributes to the model's "appends" array.
     *
     * The "retrieved" event is useful for performing actions when
     * a model is fetched from the database, allowing us to customize
     * the model's behavior during retrieval.
     *
     * @return void
     */
    
     public static function bootHasCartable()
     {
         static::retrieved(function (Model $model) {
             $model->append([
                 'is_in_cart',
                 // Add other attributes you want to append automatically
             ]);
         });
     }
}