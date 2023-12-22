<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
        'discount',
        'is_bulk_applicable',
        'number_of_items',
        'valid_until',
        'store_id',
        'used_at',
        'used_by_user_id'
    ];
    

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_cancelled' => 'boolean',
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
            get: fn () => !($this->is_cancelled) && ($this->valid_until > today() && is_null($this->used_at)),
        );
    }

    /**
     * Get all of the products that are assigned this tag.
     */
    public function products(): MorphToMany
    {
        return $this->morphedByMany(Product::class, 'couponable');
    }
 
    /**
     * Get all of the stores that are assigned this tag.
     */
    public function stores(): MorphToMany
    {
        return $this->morphedByMany(Store::class, 'couponable');
    }
}
