<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attribute extends Model
{
    use HasFactory; 
    use HasUuids;
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
     * Get all of the products that are assigned this attribute.
     */
    public function products()
    {
        return $this->morphedByMany(Product::class, 'attributable');
    }

}
