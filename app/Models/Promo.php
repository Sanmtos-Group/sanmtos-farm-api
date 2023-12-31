<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
class Promo extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => \App\Events\Promo\PromoCreated::class,
        'updated' => \App\Events\Promo\PromoUpdated::class,
    ];

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'name',
        'description',
        'discount',
        'start_datetime',
        'end_datetime',
        'store_id'
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
     * Get all of the products that are assigned this tag.
     */
    public function products(): MorphToMany
    {
        return $this->morphedByMany(Product::class, 'promoable');
    }
 
    /**
     * Get all of the stores that are assigned this tag.
     */
    public function stores(): MorphToMany
    {
        return $this->morphedByMany(Store::class, 'promoable');
    }
}
