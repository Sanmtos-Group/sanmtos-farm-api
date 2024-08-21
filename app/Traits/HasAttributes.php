<?php
namespace App\Traits; 

use App\Models\Attribute; 
use App\Models\ProductAttributeValue; 

trait HasAttributes {

    /**
     * Get all of the model's attributes.
     */
    public function attributes()
    {
        return $this->morphMany(Attribute::class, 'attributable');
    }

    /**
     * Get all of the model's attributes values.
     */
    public function attributesValues()
    {
        return $this->hasMany(ProductAttributeValue::class, 'product_id');
    }
}