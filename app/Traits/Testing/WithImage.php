<?php
namespace App\Traits\Testing; 
use App\Models\Image;

trait WithImage {

    /**
     * The image instance.
     *
     * @var \App\Models\Image
     */
    protected $image;

    /**
     * Setup up a new image instance.
     *
     * @return \App\Models\Image
     */
    protected function setUpImage(): void
    {
        $this->image = Image::factory()->create();
    }

    /**
     * @return \App\Models\Image
     */
    protected function makeImage($image_data = null ): Image
    {
        return is_array($image_data) ? Image::factory()->make($image_data) : Image::factory()->make() ;   
    }

     /**
     * Get the image instance for a given data.
     *
     * @param  array<string ,*>|null  $image_data
     * 
     * @return \App\Models\Image
     */
    public function image($image_data = null ): Image
    {
        $image = is_array($image_data) ? Image::firstOrCreate(Image::factory()->make($image_data)->toArray()) : Image::first();
        return $image ?? Image::factory()->create();
    }

    /**
     * Get a trashed image data.
     *
     * @return \App\Models\Image
     */
    public function imageTrashed(): Image 
    {
        $image_trashed = Image::onlyTrashed()->get()->first();
        if($image_trashed)
            return  $image_trashed;
            
        $image_trashed = $this->image();
        $image_trashed->delete();
        return $image_trashed;
    }

}