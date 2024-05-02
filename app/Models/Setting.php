<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'store_id',
        'html_form_element',
        'html_input_type',
        'select_options',
        'name',
        'description',
        'key',
        'value',
        'group_name',
        'settable_id',
        'settable_type',
        'allowed_editor_roles',
        'allowed_view_roles',
        'owner_feature',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'select_options' => 'json',
        'allowed_editor_roles' => 'json',
        'allowed_view_roles' => 'json',
    ];
    
    /**
     * Get the store that owns the product.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Scope a query to only include users of a given type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mix  $values
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStore($query, ...$values)
    {
        $query->withWhereHas('store', function($query) use($values){
            $query->whereIn('id', $values);

            foreach ($values as $key => $value) {
                $query->orWhere('name','like',"%".$value."%")
                ->orWhere('slug','like',"%".$value."%");
            }
                
        });
        
        return $query; 
    }
}
