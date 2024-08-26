<?php

namespace App\Http\Controllers;

use App\Http\Resources\ValueResource;
use App\Http\Requests\StoreValueRequest;
use App\Http\Requests\UpdateValueRequest;
use App\Models\Value;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ValueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $values = QueryBuilder::for(Value::class)
        ->defaultSort('created_at')
        ->allowedSorts(
            'name',
        )
        ->allowedFilters([
            'name', 
            AllowedFilter::scope('attribute')
        ])
        ->allowedIncludes([
            'categories',
        ])
        ->paginate()
        ->appends(request()->query());

        $value_resource =  ValueResource::collection($values);

        $value_resource->with['status'] = "OK";
        $value_resource->with['message'] = 'Values retrived successfully';

        return $value_resource;
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
    public function store(StoreValueRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Value $value)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Value $value)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateValueRequest $request, Value $value)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Value $value)
    {
        //
    }
}
