<?php

namespace App\Http\Controllers;

use App\Http\Resources\DiscountTypeResource;
use App\Http\Requests\StoreDiscountTypeRequest;
use App\Http\Requests\UpdateDiscountTypeRequest;
use App\Models\DiscountType;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class DiscountTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $discount_types = QueryBuilder::for(DiscountType::class)
        ->defaultSort('name')
        ->allowedSorts(
            'name', 
            'description', 
            'code',
            'created_at',
        )
        ->allowedFilters([
            'name', 
            'description', 
            'code',
            'created_at',
        ])
        ->allowedIncludes([
            'dicounts',
        ])
        ->paginate()
        ->appends(request()->query());

        $discount_type_resource =  DiscountTypeResource::collection($discount_types);

        $discount_type_resource->with['status'] = "OK";
        $discount_type_resource->with['message'] = 'Discount types retrived successfully';

        return $discount_type_resource;
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
    public function store(StoreDiscountTypeRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(DiscountType $discountType)
    {
        $discount_type_resource = new DiscountTypeResource($discountType);
        $discount_type_resource->with['message'] = 'Discount type retrieved successfully';

        return  $discount_type_resource;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DiscountType $discountType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDiscountTypeRequest $request, DiscountType $discountType)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DiscountType $discountType)
    {
        //
    }
}
