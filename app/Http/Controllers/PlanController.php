<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlanRequest;
use App\Http\Requests\UpdatePlanRequest;
use App\Http\Resources\PlanResource;
use Illuminate\Http\Request;
use LucasDotVin\Soulbscription\Enums\PeriodicityType;
use LucasDotVin\Soulbscription\Models\Feature;
use LucasDotVin\Soulbscription\Models\Plan;
use Spatie\QueryBuilder\QueryBuilder;

class PlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $plans = QueryBuilder::for(Plan::class)
            ->allowedFilters('name')
            ->defaultSort('name')
            ->allowedSorts('name', 'created_at')
            ->paginate(15);

        return PlanResource::collection($plans);
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
    public function store(StorePlanRequest $request)
    {
        $request->validated();

        $select = Plan::where('name', $request->name)->first();

        if ($select){
            return response()->json([
                'message' => "This plan is active",
                'data' => null
            ],422);
        }

        $plan = Plan::create([
            'name'             => $request->name,
            'periodicity_type' => PeriodicityType::Month, //I need to make this dynamic so admin can choose if it's month day or yearly
            'periodicity'      => $request->periodicity,
            'grace_days'       => $request->grace_days,
        ]);

        $plan_resource = new PlanResource($plan);
        $plan_resource->with['message'] = 'Plan retrieved successfully';

        return $plan_resource;
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
    public function update(UpdatePlanRequest $request, Plan $plan)
    {
        $request->validated();

        $insert = [
            "name" => $request->name,
            "grace_days" => $request->grace_days
        ];

        $save = $plan->update($insert);

        $plan_resource = new PlanResource($save);
        $plan_resource->with['message'] = "Plan updated successfully";

        return $plan_resource;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Plan $plan)
    {
        $plan->delete();

        $plan_resource = new PlanResource(null);
        $plan_resource->with['message'] = 'Plan deleted successfully';

        return $plan_resource;
    }

    public function attachFeature(Request $request){
        $request->validate([
            "feature_name" => "required|max:200|min:5",
            "plan_name" => "required|max:200|min:5",
            "charges" => "required|int"
        ]);

        $feature = Feature::where('name', $request->feature_name)->first();
        $plan = Plan::where('name', $request->plan_name)->first();

        $plan_attached = $plan->features()->attach($feature, ['charges' => $request->charges]);

        $plan_resource = new PlanResource($plan_attached);
        $plan_resource->with['message'] = "Feature attached to plan successfully";

        return $plan_resource;
    }
}
