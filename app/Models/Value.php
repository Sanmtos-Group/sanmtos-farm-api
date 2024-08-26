<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\MorphByMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Value extends Model
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
        'created' => \App\Events\Value\ValueCreated::class,
        'deleted' => \App\Events\Value\ValueDeleted::class,
        'restored' => \App\Events\Value\ValueRestored::class,
        'updated' => \App\Events\Value\ValueUpdated::class,
        'trashed' => \App\Events\Value\ValueTrashed::class,
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
        return $this->morphedByMany(Product::class, 'valuetable');
    }

    /**
     * Get all of the attributes that are assigned this value.
     * 
     * @return  \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function attributes(): MorphToMany
    {
        return $this->morphedByMany(Attribute::class, 'valuetable');
    }
    

    /**
     * Scope a query to only include products of the attribute given 
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mix  $values
     * @return void
     */
    public function scopeAttribute(Builder $query, ...$values)
    {
        $query->WhereHas('attributes', function($query) use($values){
            $query->whereIn('attributes.id', $values)
            ->orWhere(function($query)use($values){
                foreach ($values as $key => $value) {
                    $query->Where('attributes.name','like',"%".$value."%");
                }
            });
                
        });
    }

}
