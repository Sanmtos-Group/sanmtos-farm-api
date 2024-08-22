<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Attribute extends Model
{
    use HasFactory; 
    use HasUuids;
    use Sluggable;
    use SoftDeletes;

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => \App\Events\Attribute\AttributeCreated::class,
        'deleted' => \App\Events\Attribute\AttributeDeleted::class,
        'restored' => \App\Events\Attribute\AttributeRestored::class,
        'updated' => \App\Events\Attribute\AttributeUpdated::class,
        'trashed' => \App\Events\Attribute\AttributeTrashed::class,
    ];
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
    ];

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
     * Get all of the products that are assigned this attribute.
     * 
     * @return  \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function products()
    {
        return $this->morphedByMany(Product::class, 'attributable');
    }

    /**
     * Get all of the categories that are assigned this tag.
     * 
     * @return  \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function categories(): MorphToMany
    {
        return $this->morphedByMany(Category::class, 'attributable');
    }
    
    /**
     * Scope a query to only include users of a given type.
     */
    public function scopeOfCategory(Builder $query, Category $category): Builder
    {
        return $query->whereHas('categories', function($query) use($category){
            $query->where('attributables.attributable_id', $category->id);
        });
    }
}
