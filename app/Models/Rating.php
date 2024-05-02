<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;
    use HasUuids;

     /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => \App\Events\Rating\RatingCreated::class,
        'deleted' => \App\Events\Rating\RatingDeleted::class,
        'restored' => \App\Events\Rating\RatingRestored::class,
        'updated' => \App\Events\Rating\RatingUpdated::class,
        'trashed' => \App\Events\Rating\RatingTrashed::class,
    ];

    protected $fillable = [
        'user_id',
        'stars',
        'comment',
        'ratingable_id',
        'ratingable_type',
    ];

    /**
     * The attributes that should be hidden in arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Get the parent ratingable model (shop, product or image).
     */
    public function ratingable()
    {
        return $this->morphTo();
    }
}
