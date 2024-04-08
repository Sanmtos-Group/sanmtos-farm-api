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
        return $this->coupons()->where('cancelled_at', false)
        ->where('expiration_date','<', today())
        -where('used_at', null)->take(1);
    }
}