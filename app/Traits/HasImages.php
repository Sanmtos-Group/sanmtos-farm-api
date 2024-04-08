<?php
namespace App\Traits; 

use App\Models\Image; 
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
trait HasImages {

     /**
     * Get the model's image.
     */
    public function image() : MorphOne
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    /**
     * Get all of the model's images.
     */
    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }
}