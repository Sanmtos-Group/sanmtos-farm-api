<?php
namespace App\Traits; 

use App\Models\Attribute; 

trait HasAttributes {

    /**
     * Get all of the model's attributes.
     */
    public function attributes()
    {
        return $this->morphMany(Attribute::class, 'attributable');
    }
}