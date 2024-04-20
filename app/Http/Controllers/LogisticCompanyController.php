<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLogisticCompanyRequest;
use App\Http\Requests\UpdateLogisticCompanyRequest;
use App\Http\Resources\LogisticCompanyResource;
use App\Models\LogisticCompany;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class LogisticCompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $logistic_companies = QueryBuilder::for(LogisticCompany::class)
        ->defaultSort('is_default')
        ->allowedSorts(
            'name',
            'is_active',
            'is_default', 
            'created_at',
            AllowedSort::custom('recent', new \App\Models\Sorts\LatestSort()),
            AllowedSort::custom('oldest', new \App\Models\Sorts\OldestSort()),
        )
        ->allowedFilters([
            'name',
            'is_active',
            'is_default', 
            'created_at',
        ])
        ->allowedIncludes([
            'image',
        ])
        ->paginate()
        ->appends(request()->query());

        $logistic_company_resource =  LogisticCompanyResource::collection($logistic_companies);

        $logistic_company_resource->with['status'] = "OK";
        $logistic_company_resource->with['message'] = 'Payment gateways retrived successfully';

        return $logistic_company_resource;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return true;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLogisticCompanyRequest $request)
    {
        return true;
    }

    /**
     * Display the specified resource.
     */
    public function show(LogisticCompany $logisticCompany)
    {
        $logistic_company_resource = new LogisticCompanyResource($logisticCompany);
        $logistic_company_resource->with['message'] = 'Logistic company retrieved successfully';

        return  $logistic_company_resource;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LogisticCompany $logisticCompany)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLogisticCompanyRequest $request, LogisticCompany $logisticCompany)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LogisticCompany $logisticCompany)
    {
        //
    }
}
