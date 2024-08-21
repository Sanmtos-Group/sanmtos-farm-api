<?php

namespace App\Http\Controllers;

use App\Http\Resources\AttributeResource;
use App\Models\Attribute;
use App\Http\Requests\StoreAttributeRequest;
use App\Http\Requests\UpdateAttributeRequest;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class AttributeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $attributes = QueryBuilder::for(Attribute::class)
        ->defaultSort('created_at')
        ->allowedSorts(
            'name',
        )
        ->allowedFilters([
            'name', 
        ])
        ->allowedIncludes([
            'categories',
        ])
        ->paginate()
        ->appends(request()->query());

        $attribute_resource =  AttributeResource::collection($attributes);

        $attribute_resource->with['status'] = "OK";
        $attribute_resource->with['message'] = 'Attributes retrived successfully';

        return $attribute_resource;
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
        $validated = $request->validated();
        $validated['slug'] = SlugService::createSlug(Attribute::class, 'slug', $validated['slug'] ?? $validated['name']);
        
        $attribute = Attribute::create($validated);
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
        $validated = $request->validated();
        $validated['slug'] = !empty($validated['slug']?? null)? $validated['slug'] : SlugService::createSlug(Attribute::class, 'slug', $validated['name']);
        
        $attribute->update($validated);
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

    /**
     * Restore the specified resource from storage.
     * 
     * @param App\Models\Attribute $attribute
     * @return App\Http\Resources\AttributeResource $attribute_resource
     */
    public function restore($attribute)
    {
        $attribute = Attribute::withTrashed()->findOrFail($attribute);
        $attribute->restore();
        $attribute_resource = new AttributeResource($attribute);
        $attribute_resource->with['message'] = 'Attribute restored successfully';
        
        return $attribute_resource;
    }


    /**
     * Permanently remove the specified resource from storage.
     * 
     * @param App\Models\Attribute $attribute
     * @return App\Http\Resources\AttributeResource $attribute_resource
     */
    public function forceDestroy(Attribute $attribute)
    {
        $attribute->forceDelete();
        $attribute_resource = new AttributeResource(null);
        $attribute_resource->with['message'] = 'Attribute permanently deleted successfully';
        
        return $attribute_resource;
    }
}
