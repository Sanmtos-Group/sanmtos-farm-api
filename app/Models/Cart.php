<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Cart extends Model
{
    use HasFactory;
    use HasUuids;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'cartable_id',
        'cartable_type',
        'quantity',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'image_url',
        'price',
        'total_price',
    ];
    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'user_id',
        'product',
        'cartable_id',
        'cartable_type'
    ];

    /**
     * Get the parent cartable model (product or orders).
     */
    public function cartable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Determine  a cart item  image
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->products()->first()->images()->first()->url ?? null,
        );
    } 

     /**
     * Determine  a cart item  options
     */
    protected function options(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value) ,
            set: fn ($value) => json_encode($value) ,
        );
    } 

    /**
     * Determine  a cart item  price
     */
    protected function price(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->products()->first()->price,
        );
    } 

    /**
     * Determine  a cart item total price
     */
    protected function totalPrice(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->products()->first()->price * $this->quantity,
        );
    }
    
    /**
     * Get the cart product.
     */
    public function products()
    {
        return Product::where('id', $this->cartable_id);
    }
}
