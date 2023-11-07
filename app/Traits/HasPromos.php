<?php
namespace App\Traits; 

use App\Models\Promo; 

trait HasPromos {

    /**
     * Get all of the model's attributes.
     */
    public function promos()
    {
        return $this->morphMany(Promo::class, 'promoable');
    }

    /**
     * Get all of the model's attributes.
     */
    public function activePromos()
    {
        return $this->promos()->where('is_cancelled', false)
        ->where('end_datetime','>', today());
    }
}