<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStoreInvitationRequest;
use App\Http\Requests\UpdateStoreInvitationRequest;
use App\Http\Resources\StoreInvitationResource;
use App\Models\StoreInvitation;
class StoreInvitationController extends Controller
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
    public function store(StoreStoreInvitationRequest $request)
    {
        $validated = $request->validated();

        $store_invitation = StoreInvitation::create($validated);
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
