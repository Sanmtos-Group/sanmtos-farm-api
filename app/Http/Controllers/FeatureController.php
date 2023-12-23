<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFeatureRequest;
use App\Http\Requests\UpdateFeatureRequest;
use App\Http\Resources\FeatureResource;
use App\Http\Resources\PlanResource;
use Illuminate\Http\Request;
use LucasDotVin\Soulbscription\Enums\PeriodicityType;
use LucasDotVin\Soulbscription\Models\Feature;
use LucasDotVin\Soulbscription\Models\Plan;
use Spatie\QueryBuilder\QueryBuilder;

class FeatureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $feature = QueryBuilder::for(Feature::class)
            ->allowedFilters('name')
            ->defaultSort('name')
            ->allowedSorts('name', 'created_at')
            ->paginate(15);

        return new FeatureResource($feature);
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
    public function store(StoreFeatureRequest $request)
    {
        $request->validated();

        $select = Feature::where('name', $request->name)->first();

        if ($select){
            return response()->json([
                'message' => "This feature is active",
                'data' => null
            ],422);
        }

        $feature = Feature::create([
            'consumable'       => $request->consumable,
            'name'             => $request->name,
            'periodicity_type' => PeriodicityType::Month, //I need to make this dynamic so admin can choose if it's month day or yearly
            'periodicity'      => $request->periodicity,
            'postpaid' => $request->postpaid,
        ]);

        $feature_resource = new FeatureResource($feature);
        $feature_resource->with['message'] = "New feature created successfully";

        return $feature_resource;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFeatureRequest $request, Feature $feature)
    {
        $request->validated();

        $insert = [
            "consumable" => $request->consumable,
            "name" => $request->name,
            "periodicity" => $request->periodicity,
            "postpaid" => $request->postpaid,
        ];

        $save = $feature->update($insert);

        $feature_resource = new FeatureResource($save);
        $feature_resource->with['message'] = "Feature updated successfully";

        return $feature_resource;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Feature $feature)
    {
        $feature->delete();

        $plan_resource = new PlanResource(null);
        $plan_resource->with['message'] = 'Feature deleted successfully';

        return $plan_resource;
    }
}
