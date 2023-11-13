<?php

namespace App\Models;

use App\Traits\HasImages;
use App\Traits\HasPromos;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{
    use HasFactory;
    use HasImages;
    use HasPromos;
    use HasUuids;
    use Sluggable;
    use SoftDeletes;

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => \App\Events\Store\StoreCreated::class,
        'updated' => \App\Events\Store\StoreUpdated::class,
        'trashed' => \App\Events\Store\StoreTrashed::class,
    ];


     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'user_id',
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['slug'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        // 'slug',
    ];

     /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['images'];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }


    /**
     * Get the owner that owns the store.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * The staffs that works for the store.
     */
    public function staffs(): BelongsToMany
    {
        return $this->belongsToMany(User::class, StoreUser::class);
    }

    /**
     * Get the products for the store.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get the shop's most recent product.
     */
    public function recentProduct(): HasOne
    {
        return $this->hasOne(Product::class)->latestOfMany();
    }

    /**
     * Get the in promos for the store.
     */
    public function inPromos(): HasMany
    {
        return $this->hasMany(Promo::class);
    }

    /**
     * Get all of the in active promos for the store
     */
    public function inActivePromos()
    {
        
        return $this->inPromos()->where('is_cancelled', false)
        ->where('end_datetime','>', today());
    }
}
