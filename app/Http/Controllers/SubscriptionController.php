<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubscriptionResqust;
use App\Http\Requests\UpdateSubscriptionResqust;
use App\Http\Resources\SubscriptionResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use LucasDotVin\Soulbscription\Models\Subscription;
use LucasDotVin\Soulbscription\Models\Plan;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(StoreSubscriptionResqust $request)
    {
        //
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
    public function update(UpdateSubscriptionResqust $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subscription $subscription)
    {
        //
    }

    /**
     * Subscribe User
     */
    public function subscribe(Request $request){
        $user = Auth::user();
        $plan = Plan::find($request->plan_id);
        $subscription = $user->subscribeTo($plan);

        $sub_resource = new SubscriptionResource($subscription);
        $sub_resource->with['message'] = "You have successfully subscribed to" . $plan->name . "plan";

        return $sub_resource;
    }

    /**
     * Renew plan
     */
    public function renewPlan(){
        $subscriber = Auth::user();
        $subscription = $subscriber->lastSubscription()->renew();

        $sub_resource = new SubscriptionResource($subscription);
        $sub_resource->with['message'] = "Your plan was renewed successfully";

        return $sub_resource;
    }

    /**
     * Switch a plan to a specific plan
     */
    public function switchPlan(Request $request){

        $plan = Plan::where('name', $request->plan)->first();
        $user = Auth::user();

        $user->switchTo($plan, immediately: true);

        return response()->json([
            'data' => $plan,
            'message' => "Your plan is switched successfully"
        ], 201);
    }

    /**
     * Cancel subscription
     */
    public function cancelPlan(){
        $subscriber = Auth::user();
        $subscriber->subscription->cancel();

        $sub_resource = new SubscriptionResource(null);
        $sub_resource->with['message'] = "Subscription canceled successfully";

        return $sub_resource;
    }
}
