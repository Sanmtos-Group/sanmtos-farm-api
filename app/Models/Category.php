<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;
    use Sluggable;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'slug',
        'parent_category_id'
    ];

     /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['image'];

    /**
     * Determine product image
     */
    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->products()->inRandomOrder()->first()?->toArray()['images'][0]?? null
        );
    }

    /**
     * Get the sub categories for the category.
     */
    public function subCategories(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_category_id');
    }

    /**
     * Get the parent categories for the category.
     */
    public function parentCategories(): BelongsTo
    {
        return $this->hasMany(Category::class, 'parent_category_id');
    }

    /**
     * Get the products for the category.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }
}
