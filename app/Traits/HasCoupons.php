<?php
namespace App\Traits; 

use App\Models\Coupon; 
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasCoupons {

    /**
     * Get all of the coupons for the model.
     */
    public function coupons() : MorphToMany
    {
        return $this->morphToMany(Coupon::class, 'couponable');
    }

    /**
     * Get all of the active Coupon
     */
    public function activeCoupon()
    {
        return $this->coupons()->where('is_cancelled', false)
        ->where('valid_until','>', today())->take(1);
    }
}