<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskResource;
use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Task;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {  
        $categories = QueryBuilder::for(Category::class)
            ->defaultSort('name')
            ->allowedSorts(['name', 'created_at'])
            ->allowedFilters([
                AllowedFilter::scope('attribute'),
            ])
            ->allowedIncludes([
                'attributes',
            ])
            ->paginate(10);
        return CategoryResource::collection($categories);


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
    public function store(StoreCategoryRequest $request)
    {
        $request->validated();

        $select = Category::where('name', $request->name)->first();

        if ($select){
            return response()->json([
                'message' => 'This category is active',
                'status' => 'Fail',
                'data' => null
            ], 422);
        }

        $insert = [
            "name" => $request->name,
            "description" => $request->description,
            'slug' => SlugService::createSlug(Category::class, 'slug', $request->name),
            "parent_category_id" => $request->parent_category_id

        ];

        $category = Category::create($insert);

        $category_resource = new CategoryResource($category);
        $category_resource->with['message'] = "Category Created Successfully...";

        return $category_resource;
    }

    /**
     * Display the specified resource.
     */
    public function show($category)
    {
        $category = Category::where('id', $category)->orWhere('slug', $category)->firstOrFail();
        return new CategoryResource($category);

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, $category)
    {
        $request->validated();

        $category = Category::where('id', $category)->orWhere('slug', $category)->firstOrFail();


        $insert = [
            "name" => $request->name,
            "description" => $request->description,
            'slug' => SlugService::createSlug(Category::class, 'slug', $request->name),
            "parent_category_id" => $request->parent_category_id

        ];
        $category->update($insert);
        $category_resource = new CategoryResource($category);
        $category_resource->with['message'] = 'Category Updated Successfully...';

        return $category_resource;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();

        $category_resource = new CategoryResource(null);
        $category_resource->with['message'] = 'Category deleted successfully...';

        return $category_resource;
    }
}
