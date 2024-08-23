<?php
namespace App\Traits; 

use App\Models\Attribute; 
use App\Models\ProductAttributeValue; 
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Builder;

trait HasAttributes {

    /**
     * Get all of the model's attributes.
     * 
     * @return Illuminate\Database\Eloquent\Relations\MorphToMany;
     */
    public function attributes(): MorphToMany
    {
        return $this->morphToMany(Attribute::class, 'attributable');
    }

    /**
     * Get all of the model's attributes values.
     */
    public function attributesValues()
    {
        return $this->hasMany(ProductAttributeValue::class, 'product_id');
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