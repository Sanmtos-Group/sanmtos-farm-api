<?php
namespace App\Traits; 

use App\Models\Image; 
use App\Services\CloudinaryService;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Http\File;
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

    public function uploadImageToCloudinary(File $image, String $path='images', array $options=[])
    {
        // upload to cloudinary
        $uploaded_image = CloudinaryService::uploadImage($image, $path, $options);

        // save image information
        $image_data['url'] = $uploaded_image->getSecurePath();
        $image_data['imageable_id'] = $this->id;
        $image_data['imageable_type'] = $this::class;

        Image::create($image_data);
    }

    public function deleteCloudinaryImages()
    {
        $images = $this->images()->where('url', 'like', '%cloudinary%')->get();

        foreach ($images as $key => $image) 
        {
            CloudinaryService::destroy($image->url);
            $image->forceDelete();
        }
    }
}