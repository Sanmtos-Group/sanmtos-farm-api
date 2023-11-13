<?php
namespace App\Traits; 

use App\Models\Promo; 
use Illuminate\Database\Eloquent\Relations\MorphToMany;
trait HasPromos {

    /**
     * Get all of the promos for the model.
     */
    public function promos() : MorphToMany
    {
        return $this->morphToMany(Promo::class, 'promoable');
    }

    /**
     * Get all of the active promo
     */
    public function activePromo()
    {
        
        return $this->promos()->where('is_cancelled', false)
        ->where('end_datetime','>', today())->take(1);
    }
}