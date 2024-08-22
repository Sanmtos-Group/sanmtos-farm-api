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
use Illuminate\Support\Str;
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
            AllowedFilter::scope('ofCategory')
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

        $categories = [];
        
        // clean category ids for syncing 
        foreach ($validated['category_ids']?? [] as $key=>$category_id) 
        {
            $categories [$category_id] = [
                // Other pivot table attributes if needed
                'id' => Str::uuid()->toString(), // Generate UUID for the pivot ID
            ];
        }
        $attribute->categories()->syncWithoutDetaching($categories);
        $attribute->categories;  
        
        $attribute_resource = new AttributeResource($attribute);
        $attribute_resource->with['message'] = 'Attribute created successfully';

        return $attribute_resource;
    }

    /**
     * Display the specified resource.
     */
    public function show(Attribute $attribute)
    {
        if(request()->has('include'))
        {
            foreach (explode(',', request()->include) as $key => $include) 
            {
               try {
                $attribute->load($include);
               } catch (\Throwable $th) {
                    continue;
               }
            }
        }

        if(request()->has('append'))
        {
            foreach (explode(',', request()->append) as $key => $attrs) 
            {
                if(method_exists($attribute, $attrs) || array_key_exists($attrs, $attribute->getAttributes()))
                {
                    $attribute->append($attrs);
                }
            }
        }

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
