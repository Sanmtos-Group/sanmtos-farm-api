<?php
namespace App\Traits; 

use App\Models\Like; 
use Illuminate\Database\Eloquent\Casts\Attribute;
trait HasReviews {

    /**
     * Get all the likes for the likeable.
     */
    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }
    
    /**
     * Get the likeable's most recent like.
     */
    public function latestLike()
    {
        return $this->hasOne(Like::class)->latestOfMany();
    }

     /**
     * Determine if the current user has liked the likeable
     */
    protected function isLiked(): Attribute
    {
        return Attribute::make(
            get: fn () => is_null(auth()->user()) ? false: (is_null($this->likes()->where('user_id', auth()->user()->id)->first()) ? false: true),
        );
    }
    
}