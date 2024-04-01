<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
class Coupon extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => \App\Events\Coupon\CouponCreated::class,
        'updated' => \App\Events\Coupon\CouponCreated::class,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
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
    ];
    

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'is_valid', 
    ];

    

     /**
     * Determine if a coupon is valid
     */
    protected function isValid(): Attribute
    {
        return Attribute::make(
            get: fn () => is_null($this->cancelled_at) 
                && $this->expiration_date >= today() 
        );
    }

    /**
     * Get the store that owns the coupon
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
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
     * Get the discount type that owns the coupon
     */
    public function discountType(): BelongsTo
    {
        return $this->belongsTo(DiscountType::class);
    }

    /**
     * Scope the coupon by the discount type 
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mix  $values
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDiscountType(Builder $query, ...$values)
    {
        $query->whereHas('discountType', function(Builder $query) use($values)
        {
            $query->whereIn('discount_types.id', $values);
            foreach ($values as $key => $value) 
            {
                $query->orWhere('discount_types.name','like',"%".$value."%")
                ->orWhere('discount_types.description','like',"%".$value."%")
                ->orWhere('users.code','like',"%".$value."%");
            }
        });
       
        return $query; 
    }

    /**
     * Get the recipients of the coupon.
     */
    public function recipients(): MorphToMany
    {
        return $this->morphedByMany(User::class, 'couponable');
    }

    /**
     * Scope the coupon by the recipients 
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mix  $values
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRecipients(Builder $query, ...$values)
    {
        $query->whereHas('recipients', function(Builder $query) use($values)
        {
            $query->whereIn('users.id', $values);
            foreach ($values as $key => $value) 
            {
                $query->orWhere('users.first_name','like',"%".$value."%")
                ->orWhere('users.last_name','like',"%".$value."%")
                ->orWhere('users.email','like',"%".$value."%")
                ->orWhere('users.phone_number','like',"%".$value."%")
                ->orWhere('users.gender','like',"%".$value."%");
            }
        });
       
        return $query; 
    }

     /**
     * Get all applicable products of the coupon.
     */
    public function applicableProducts(): MorphToMany
    {
        return $this->morphedByMany(Product::class, 'couponable');
    }

     /**
     * Scope the coupon by the recipients 
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mix  $values
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApplicableProducts(Builder $query, ...$values)
    {
        $query->whereHas('applicableProducts', function(Builder $query) use($values)
        {
            $query->whereIn('products.id', $values);
            foreach ($values as $key => $value) 
            {
                $query->orWhere('products.name','like',"%".$value."%");
            }
        });
       
        return $query; 
    }


     /**
     * Get all applicable products of the coupon.
     */
    public function applicableCategories(): MorphToMany
    {
        return $this->morphedByMany(Category::class, 'couponable');
    }

     /**
     * Scope the coupon by the recipients 
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mix  $values
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApplicableCategories(Builder $query, ...$values)
    {
        $query->whereHas('applicableCategories', function(Builder $query) use($values)
        {
            $query->whereIn('categories.id', $values);
            foreach ($values as $key => $value) 
            {
                $query->orWhere('categories.name','like',"%".$value."%");
            }
        });
       
        return $query; 
    }


    /**
     * Get the usages of the coupon.
     */
    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }
}
