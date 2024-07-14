<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStoreInvitationRequest;
use App\Http\Requests\UpdateStoreInvitationRequest;
use App\Http\Resources\StoreInvitationResource;
use App\Models\StoreInvitation;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class StoreInvitationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $store_invitations = QueryBuilder::for(StoreInvitation::class)
        ->defaultSort('created_at')
        ->allowedSorts(
            'email',
            AllowedSort::custom('recent', new \App\Models\Sorts\LatestSort()),
            AllowedSort::custom('oldest', new \App\Models\Sorts\OldestSort()),
        )
        ->allowedFilters([
            'email', 
            'created_at',
            AllowedFilter::exact('store_id'),
            AllowedFilter::scope('recent'),
        ])
        ->allowedIncludes([
            'store',
            'user',
        ])
        ->paginate()
        ->appends(request()->query());

        $product_resource =  StoreInvitationResource::collection($store_invitations);

        $product_resource->with['status'] = "OK";
        $product_resource->with['message'] = 'Store Invitations retrived successfully';

        return $product_resource;
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
    public function store(StoreStoreInvitationRequest $request)
    {
        $validated = $request->validated();

        $store_invitation = StoreInvitation::create($validated);
        $store_invitation->refresh();
        $store_invitation_resource = new StoreInvitationResource($store_invitation);
        $store_invitation_resource->with['message'] = 'Store Invitation created successfully';

        return $store_invitation_resource;

    }

    /**
     * Display the specified resource.
     */
    public function show(StoreInvitation $storeInvitation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StoreInvitation $storeInvitation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStoreInvitationRequest $request, StoreInvitation $storeInvitation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StoreInvitation $storeInvitation)
    {
        //
    }
}
