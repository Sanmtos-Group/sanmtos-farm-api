<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Orderable extends Pivot
{
    use HasFactory;
    use HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'orderables';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'orderable_id',
        'orderable_type',
        'quantity',
        'price', 
        'total_price',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'orderable_item',
    ];

    /**
     * Get the parent orderables model (post or video).
     */
    public function orderables(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     *  meta attribute
     */
    protected function orderableItem(): Attribute
    {
        return Attribute::make(
            get: function() {

                if(!class_exists($this->orderable_type))
                {
                    return null;
                }
                
                return $this->orderable_type::find($this->orderable_id);
                
            }
        );
    }

    
}
