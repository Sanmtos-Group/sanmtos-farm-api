<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNotificationPreferenceRequest;
use App\Http\Requests\UpdateNotificationPreferenceRequest;
use App\Http\Resources\NotificationPreferenceResource;
use App\Models\NotificationPreference;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class NotificationPreferenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $notification_preferences = QueryBuilder::for(NotificationPreference::class)
        ->defaultSort('created_at')
        ->allowedSorts(
            'code', 
            'description', 
            'channel',
            'type',
            AllowedSort::custom('recent', new \App\Models\Sorts\LatestSort()),
            AllowedSort::custom('oldest', new \App\Models\Sorts\OldestSort()),
        )
        ->allowedFilters([
            'code', 
            'description', 
            'channel',
            'type',
            'created_at',
        ])
        ->allowedIncludes([
            'subscribers',
        ])
        ->paginate()
        ->appends(request()->query());

        $notification_preference_resource =  NotificationPreferenceResource::collection($notification_preferences);

        $notification_preference_resource->with['status'] = "OK";
        $notification_preference_resource->with['message'] = 'Notification preferences retrived successfully';

        return $notification_preference_resource;
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
    public function store(StoreNotificationPreferenceRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(NotificationPreference $notificationPreference)
    {
        $notification_preference_resource =  new NotificationPreferenceResource($notificationPreference);

        $notification_preference_resource->with['message'] = 'Notification preference retrived successfully';

        return  $notification_preference_resource;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(NotificationPreference $notificationPreference)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNotificationPreferenceRequest $request, NotificationPreference $notificationPreference)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NotificationPreference $notificationPreference)
    {
        //
    }
}
