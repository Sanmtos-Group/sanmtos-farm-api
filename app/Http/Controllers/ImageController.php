<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Http\Requests\StoreImageRequest;
use App\Http\Requests\UpdateImageRequest;
use App\Http\Resources\ImageResource;

class ImageController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * @return App\Http\Resources\ImageResource $image_resource
     */
    public function index()
    {
        $images = Image::all();
        $image_resource = new ImageResource($images);
        return $image_resource;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     * 
     * @param App\Http\Requests\StoreImageRequest $request
     * @return App\Http\Resources\ImageResource $image_resource
     */
    public function store(StoreImageRequest $request)
    {
        $image = Image::create($request->validated());
        $image_resource = new ImageResource($image);
        $image_resource->with['message'] = 'Image created successfully';

        return $image_resource;
    }

    /**
     * Display the specified resource.
     * 
     * @param App\Models\Image $image
     * @return App\Http\Resources\ImageResource $image_resource
     */
    public function show(Image $image)
    {
        $image_resource = new ImageResource($image);
        $image_resource->with['message'] = 'Image retrieved successfully';

        return  $image_resource;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Image $image)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     * 
     * @param App\Models\Image $image
     * @return App\Http\Resources\ImageResource $image_resource
     */
    public function update(UpdateImageRequest $request, Image $image)
    { 
        $image->update($request->validated());
        $image_resource = new ImageResource($image);
        $image_resource->with['message'] = 'Image updated successfully';

        return $image_resource;
    }

    /**
     * Remove the specified resource from storage.
     * 
     * @param App\Models\Image $image
     * @return App\Http\Resources\ImageResource $image_resource
     */
    public function destroy(Image $image)
    {
        $image->delete();
        $image_resource = new ImageResource(null);
        $image_resource->with['message'] = 'Image deleted successfully';
        
        return $image_resource;
    }

    /**
     * Restore the specified resource from storage.
     * 
     * @param App\Models\Image $image
     * @return App\Http\Resources\ImageResource $image_resource
     */
    public function restore($image)
    {
        $image = Image::withTrashed()->findOrFail($image);
        $image->restore();
        $image_resource = new ImageResource($image);
        $image_resource->with['message'] = 'Image restored successfully';
        
        return $image_resource;
    }


    /**
     * Permanently remove the specified resource from storage.
     * 
     * @param App\Models\Image $image
     * @return App\Http\Resources\ImageResource $image_resource
     */
    public function forceDestroy(Image $image)
    {
        $image->forceDelete();
        $image_resource = new ImageResource(null);
        $image_resource->with['message'] = 'Image permanently deleted successfully';
        
        return $image_resource;
    }
}
