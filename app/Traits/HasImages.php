<?php
namespace App\Traits; 

use App\Models\Image; 

trait HasImages {

    /**
     * Get all of the model's images.
     */
    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }
}