<?php
namespace App\Traits; 

use App\Models\Attribute; 

trait HasReviews {

    /**
     * Get all the likes for the production.
     */
    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    /**
     * Get the product's most recent like.
     */
    public function latestLike()
    {
        return $this->hasOne(Like::class)->latestOfMany();
    }
}