<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSettingRequest;
use App\Http\Requests\UpdateSettingRequest;
use App\Http\Resources\SettingResource;
use App\Models\Setting;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;
class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $settings = QueryBuilder::for(Setting::class)
        ->defaultSort('created_at')
        ->allowedSorts(
            'html_input_type',
            'select_options',
            'name',
            'description',
            'key',
            'value',
            'group_name',
            'settable_id',
            'settable_type',
            'allowed_editor_roles',
            'allowed_view_roles',
            'owner_feature',
            'created_at',
            AllowedSort::custom('recent', new \App\Models\Sorts\LatestSort()),
            AllowedSort::custom('oldest', new \App\Models\Sorts\OldestSort()),
        )
        ->allowedFilters([
            'html_input_type',
            'select_options',
            'name',
            'description',
            'key',
            'value',
            'group_name',
            'settable_id',
            'settable_type',
            'allowed_editor_roles',
            'allowed_view_roles',
            'owner_feature',
            'created_at',
            AllowedFilter::scope('store'),
        ])
        ->allowedIncludes([
            'store',
        ])
        ->paginate()
        ->appends(request()->query());

        $setting_resource =  SettingResource::collection($settings);

        $setting_resource->with['status'] = "OK";
        $setting_resource->with['message'] = 'Settings retrieved successfully';

        return $setting_resource;
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
    public function store(StoreSettingRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Setting $setting)
    {
        if(request()->has('include'))
        {
            foreach (explode(',', request()->include) as $key => $value) {
               $setting->{$value};
            }
        }

        $setting_resource = new SettingResource($setting);
        $setting_resource->with['message'] = 'Setting retrieved successfully';

        return  $setting_resource;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Setting $setting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSettingRequest $request, Setting $setting)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Setting $setting)
    {
        //
    }
}
