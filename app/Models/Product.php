<?php

namespace App\Models;

use App\Traits\HasAttributes;
use App\Traits\HasCartable;
use App\Traits\HasCoupons;
use App\Traits\HasImages;
use App\Traits\HasPromos;
use App\Traits\HasReviews;
use Illuminate\Database\Eloquent\Casts\Attribute as CastAttribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;/*  */
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasAttributes;
    use HasCartable;
    use HasCoupons;
    use HasFactory;
    use HasImages;
    use HasPromos;
    use HasReviews;
    use HasUuids;
    use SoftDeletes;

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => \App\Events\Product\ProductCreated::class,
        'updated' => \App\Events\Product\ProductUpdated::class,
        'trashed' => \App\Events\Product\ProductTrashed::class,
    ];

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'short_description',
        'weight',
        'width',
        'length',
        'height',
        'volume',
        'shelf_life',
        'price',
        'regular_price',
        'currency',
        'quantity',
        'status',
        'category_id',
        'store_id',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
     protected $appends = [
        // 'country'
    ];
     

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['images', 'activePromo'];
    

    /**
     * ----------------------------------------------------------------------------------------------------
     * Model's Accessors and Mutators
     * ----------------------------------------------------------------------------------------------------
     */

    /**
     * Get the store that owns the product.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Get the category that the product belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the verifier of the product.
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verifier_id');
    }

    /**
     * ----------------------------------------------------------------------------------------------------
     * Model's Accessors and Mutators
     * ----------------------------------------------------------------------------------------------------
     */

    /**
     * Determine product discount
     */
    protected function discount(): CastAttribute
    {
        return CastAttribute::make(
            get: fn () => !is_null($this->regular_price) ?  round(($this->regular_price - $this->price)/ $this->regular_price * 100, 2): 0,
            set: fn () => !is_null($this->regular_price) ?  round(($this->regular_price - $this->price)/ $this->regular_price * 100, 2): 0,
        );
    }

    /**
     * Determine product discount
     */
    protected function volume(): CastAttribute
    {
        return CastAttribute::make(
            get: fn () => $this->length * $this->width * $this->height,
            set: fn () => $this->length * $this->width * $this->height,
        );
    }

    /**
     * Determine product discount
     */
    protected function country(): CastAttribute
    {
        return CastAttribute::make(
            get: fn () =>  $this->store->address->country?? null
        );
    }

    /**
     * ----------------------------------------------------------------------------------------------------
     * Model's scope
     * ----------------------------------------------------------------------------------------------------
     */
    

    /**
     * Scope a query to only include products of a given type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $min
     * @return void
     */
    public function scopeMinPrice($query, $min)
    {
        $query->where('price','>=', $min);
    }

    /**
     * Scope a query to only include products of a given type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $max
     * @return void
     */
    public function scopeMaxPrice($query, $max)
    {
        $query->where('price','<=',$max);
    }

    /**
     * Scope a query to only include products of a given type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $min
     * @param  mixed  $max
     * @return void
     */
    public function scopePriceBetween($query, $min, $max)
    {
        return $query->where('price','>=', $min)->where('price','<=',$max);
    }

    /**
     * Scope a query to only include products of a given type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mix  $values
     * @return void
     */
    public function scopeStore($query, ...$values)
    {
        $query->withWhereHas('store', function($query) use($values){
            $query->whereIn('id', $values)
            ->orWhere(function($query)use($values){
                foreach ($values as $key => $value) {
                    $query->Where('name','like',"%".$value."%")
                    ->orWhere('slug','like',"%".$value."%");
                }
            });
        });
    }


    /**
     * Scope a query to only include products of a given type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mix  $values
     * @return void
     */
    public function scopeCategory($query, ...$values)
    {
        $query->withWhereHas('category', function($query) use($values){
            $query->whereIn('id', $values)
            ->orWhere(function($query)use($values){
                foreach ($values as $key => $value) {
                    $query->Where('name','like',"%".$value."%");
                }
            });
        });
    }

    /**
     * Scope a query to only include products of the country given 
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mix  $values
     * @return void
     */
    public function scopeCountry($query, ...$values)
    {
        $query->WhereHas('store.address.country', function($query) use($values){
            $query->whereIn('id', $values)
            ->orWhere(function($query)use($values){
                foreach ($values as $key => $value) {
                    $query->Where('name','like',"%".$value."%");
                }
            });
                
        });
    }

     /**
     * Scope a query to only include products of the state given 
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mix  $values
     * @return void
     */
    public function scopeState($query, ...$values)
    {
        $query->WhereHas('store.address.country', function($query) use($values){
            $query->whereIn('state', $values)
            ->orWhere(function($query)use($values){
                foreach ($values as $key => $value) {
                    $query->Where('name','like',"%".$value."%");
                }
            });
        }); 
    }
}
