<?php

namespace App\Http\Controllers;

use App\Http\Resources\AttributeResource;
use App\Http\Requests\StoreAttributeRequest;
use App\Http\Requests\UpdateAttributeRequest;
use App\Http\Requests\StoreAttributableRequest;
use App\Http\Requests\StoreValuetableRequest;
use App\Models\Attribute;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
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

    /**
     * Add to attribute  categories
     *
     * @param App\Models\Attribute $attribute
     * @param App\Http\Requests\StoreAttributableRequest $request
     * @return AttributeResource $attribute_resource
     */
    public function attachCategories(Attribute $attribute, StoreAttributableRequest $request )
    {
        $validated = $request->validated();

            // attach by multiple category ids
        if(array_key_exists('category_ids', $validated))
        {
                $attribute->categories()->syncWithoutDetaching($validated['category_ids']);
        }
       
        // attach by multiple new categories
        if(array_key_exists('categories', $validated))
        {
            foreach ($validated['categories'] as $key=>$value) 
            {
                $attribute->categories()->updateOrCreate(
                    $attributes=['name' => $value], 
                    $values=['name'=>$value]
                );
            }
        }

        $attribute->categories = $attribute->categories()->paginate()
        ->appends(request()->query());    

        $attribute_resource = new AttributeResource($attribute);
        $attribute_resource->with['message'] = 'Category(ies) attached to attribute succesfully';
        return $attribute_resource;
    }

    /**
     * Detached categories to attribute
     *
     * @param App\Models\Attribute $attribute
     * @param Illuminatie\Http\Request $request
     * @return App\Http\Resources\ProductResource $product_resource
     */
    public function detachCategories(Attribute $attribute, Request $request )
    {
        // detach by multiple category ids
        if($request->has('category_ids'))
        {
            $attribute->categories()->detach($request->category_ids);
        }
       
        $attribute->categories = $attribute->categories()->paginate()
        ->appends(request()->query());

        $attribute_resource = new AttributeResource($attribute);
        $attribute_resource->with['message'] = 'Category(ies) detached from attribute succesfully';
        return $attribute_resource;
    }

    /**
     * Add to attribute  values
     *
     * @param App\Models\Attribute $attribute
     * @param App\Http\Requests\StoreValuetableRequest $request
     * @return AttributeResource $attribute_resource
     */
    public function attachValues(Attribute $attribute, StoreValuetableRequest $request )
    {
        $validated = $request->validated();

        // attach by multiple value ids
       if(array_key_exists('value_ids', $validated))
       {
            $attribute->values()->syncWithoutDetaching($validated['value_ids']);
       }

       // attach by multiple new values
       if(array_key_exists('values', $validated))
       {
            foreach ($validated['values'] as $key=>$value) 
            {
                $attribute->values()->updateOrCreate(
                    $attributes=['name' => $value], 
                    $values=['name'=>$value]
                );
            }
       }
       
        $attribute->values = $attribute->values()->paginate()
        ->appends(request()->query());    

        $attribute_resource = new AttributeResource($attribute);
        $attribute_resource->with['message'] = 'Value(s) attached to attribute succesfully';
        return $attribute_resource;
    }

    /**
     * Detached values to attribute
     *
     * @param App\Models\Attribute $attribute
     * @param Illuminatie\Http\Request $request
     * @return App\Http\Resources\ProductResource $product_resource
     */
    public function detachValues(Attribute $attribute, Request $request )
    {
        // detach by multiple value ids
        if($request->has('value_ids'))
        {
            $attribute->values()->detach($request->value_ids);
        }
       
        $attribute->values = $attribute->values()->paginate()
        ->appends(request()->query());

        $attribute_resource = new AttributeResource($attribute);
        $attribute_resource->with['message'] = 'Value(s) detached from attribute succesfully';
        return $attribute_resource;
    }
}
