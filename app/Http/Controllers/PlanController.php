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
        $validated = $request->validated();

        $plan = Plan::create($validated);

        $plan_resource = new PlanResource($plan);
        $plan_resource->with['message'] = 'Plan created successfully';

        return $plan_resource;
    }

    /**
     * Display the specified resource.
     */
    public function show(Plan $plan)
    {
        $plan_resource = new PlanResource($plan);
        $plan_resource->with['message'] = 'Plan retrived successfully';

        return $plan_resource;
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePlanRequest $request, Plan $plan)
    {
       $plan->update($request->validated());

        $plan_resource = new PlanResource($plan);
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

    public function attachFeature(Request $request, Plan $plan){

        $rule ["charges"] = "required|int";

        // bulk attach features validation rule
       if(array_key_exists('feature_ids', $request->all()))
       { 
            $rule['feature_ids'] = 'required|array';
            $rule['feature_ids.*'] = 'integer|exists:features,id';
       }
        // single attach feature validation rule
       else  
       {
            $rule['feature_id'] = 'required|integer|exists:features,id';
       }

       $validated = $request->validate($rule);

        // bulk attach features validation rule
        if(array_key_exists('feature_ids', $validated))
        { 
            $plan->features()->syncWithPivotValues($ids=$validated['feature_ids'], $values = ['charges'=>$validated['charges']]);
        }
        else {
            $plan->features()->syncWithPivotValues($ids=$validated['feature_id'], $values = ['charges'=>$validated['charges']]);
        }

        // attach the features to the response;
        $plan->features;

        $plan_resource = new PlanResource($plan);
        $plan_resource->with['message'] = "Feature attached to plan successfully";

        return $plan_resource;
    }

    public function detachFeature(Request $request, Plan $plan){


        // bulk detach features validation rule
       if(array_key_exists('feature_ids', $request->all()))
       { 
            $rule['feature_ids'] = 'required|array';
            $rule['feature_ids.*'] = 'integer|exists:features,id';
       }
        // single detach feature validation rule
       else  
       {
            $rule['feature_id'] = 'required|integer|exists:features,id';
       }

       $validated = $request->validate($rule);

        // bulk detach features validation rule
        if(array_key_exists('feature_ids', $validated))
        { 
            $plan->features()->detach($ids=$validated['feature_ids']);
        }
        else {
            $plan->features()->detach($ids=$validated['feature_id']);
        }

        // attach the features to the response;
        $plan->features;

        $plan_resource = new PlanResource($plan);
        $plan_resource->with['message'] = "Feature detached from plan successfully";

        return $plan_resource;
    }
}
