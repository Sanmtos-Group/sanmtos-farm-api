<?php

namespace App\Models;

use App\Traits\HasAttributes;
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
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\SoftDeletes;
class Product extends Model
{
    use HasAttributes;
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
        'volume',
        'price',
        'currency',
        'regular_price',
        'category_id',
        'store_id',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
     protected $appends = ['is_liked'];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['images', 'activePromo'];

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
     * Scope a query to only include users of a given type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $min
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMinPrice($query, $min)
    {
        return $query->where('price','>=', $min);
    }

    /**
     * Scope a query to only include users of a given type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $max
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMaxPrice($query, $max)
    {
        return $query->where('price','<=',$max);
    }

    /**
     * Scope a query to only include users of a given type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $min
     * @param  mixed  $max
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePriceBetween($query, $min, $max)
    {
        return $query->where('price','>=', $min)->where('price','<=',$max);
    }

     /**
     * Scope a query to only include users of a given type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mix  $values
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStore($query, ...$values)
    {
        $query->withWhereHas('store', function($query) use($values){
            $query->whereIn('id', $values);

            foreach ($values as $key => $value) {
                $query->orWhere('name','like',"%".$value."%")
                ->orWhere('slug','like',"%".$value."%");
            }
                
        });
        
        return $query; 
    }


    /**
     * Scope a query to only include users of a given type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mix  $values
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCategory($query, ...$values)
    {
        $query->withWhereHas('category', function($query) use($values){
            $query->whereIn('id', $values);

            foreach ($values as $key => $value) {
                $query->orWhere('name','like',"%".$value."%")
                ->orWhere('slug','like',"%".$value."%");
            }
                
        });
        
        return $query; 
    }
}
