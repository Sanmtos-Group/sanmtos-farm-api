<?php

namespace App\Http\Controllers;

use App\Http\Resources\AttributeResource;
use App\Models\Attribute;
use App\Http\Requests\StoreAttributeRequest;
use App\Http\Requests\UpdateAttributeRequest;
class AttributeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $attributes = Attribute::all();
        return AttributeResource::collection($attributes);
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
     */
    public function store(StoreAttributeRequest $request)
    {
        $attribute = Attribute::create($request->validated());
        $attribute_resource = new AttributeResource($attribute);
        $attribute_resource->with['message'] = 'Attribute created successfully';

        return $attribute_resource;
    }

    /**
     * Display the specified resource.
     */
    public function show(Attribute $attribute)
    {
        $attribute_resource = new AttributeResource($attribute);
        $attribute_resource->with['message'] = 'Attribute retrieved successfully';

        return $attribute_resource;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attribute $attribute)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAttributeRequest $request, Attribute $attribute)
    {
        $attribute->update($request->validated());
        $attribute_resource = new AttributeResource($attribute);
        $attribute_resource->with['message'] = 'Attribute updated successfully';

        return $attribute_resource;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attribute $attribute)
    {
        $attribute->delete();
        $attribute_resource = new AttributeResource(null);
        $attribute_resource->with['message'] = 'Attribute deleted successfully';

        return $attribute_resource;
    }
}
