<?php

namespace App\Models;

use App\Traits\HasAttributes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasAttributes;
    use HasFactory;
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
        'discount',
        'category_id',
        'store_id',
    ];

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
