<?php

namespace App\Models;

use App\Traits\HasAttributes;
use App\Traits\HasCoupons;
use App\Traits\HasImages;
use App\Traits\HasPromos;

use Illuminate\Database\Eloquent\Casts\Attribute as CastAttribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasAttributes;
    use HasCoupons;
    use HasFactory;
    use HasImages;
    use HasPromos;
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
        'price',
        'currency',
        'regular_price',
        // 'discount',
        'category_id',
        'store_id',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['images', 'activePromo',];

     /**
     * Determine if a user owns a store
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
}
