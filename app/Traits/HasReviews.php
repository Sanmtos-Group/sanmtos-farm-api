<?php
namespace App\Traits; 

use App\Models\Like; 
use Illuminate\Database\Eloquent\Model;
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

    /**
     * Get the models's total likes.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function totalLikes(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->likes()->count(),
        );
    } 
    
    /**
     * Boot the HasReviews trait for a model.
     *
     * This method is automatically called when the model is booted.
     * It hooks into the "retrieved" event, which is fired when a model
     * instance is retrieved from the database. In this method, we
     * dynamically append the specified attributes to the model's "appends" array.
     *
     * The "retrieved" event is useful for performing actions when
     * a model is fetched from the database, allowing us to customize
     * the model's behavior during retrieval.
     *
     * @return void
     */
    
    public static function bootHasReviews()
    {
        static::retrieved(function (Model $model) {
            $model->append([
                'is_liked',
                'total_likes',
                // Add other attributes you want to append automatically
            ]);
        });
    }
}