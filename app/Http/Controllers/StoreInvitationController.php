<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStoreInvitationRequest;
use App\Http\Requests\UpdateStoreInvitationRequest;
use App\Http\Resources\StoreInvitationResource;
use App\Models\StoreInvitation;
use App\Models\Role;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Facades\DB;

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

        $store_invitation_resource =  StoreInvitationResource::collection($store_invitations);

        $store_invitation_resource->with['status'] = "OK";
        $store_invitation_resource->with['message'] = 'Store Invitations retrived successfully';

        return $store_invitation_resource;
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
    public function show(StoreInvitation $store_invitation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStoreInvitationRequest $request, StoreInvitation $store_invitation)
    {
        //
    }

    /**
     * Accept the specified resource invitation.
     */
    public function accept(StoreInvitation $store_invitation)
    {
        if(!is_null($store_invitation->declined_at))
        {

            $store_invitation->status = 'declined';
            $store_invitation->save();

            return response()->json([
                "data" => null,
                'status' => 'Failed',
                "message" => "Invitation declined on ".$store_invitation->declined_at
            ], 422);
        }

        try {

            DB::beginTransaction();

            if(is_null($store_invitation->accepted_at))
            {

                $store_invitation->status = 'accepted';
                $store_invitation->accepted_at = now();
                $store_invitation->save();
    
                $store_invitation->load(['store','user']);
    
                if(!is_null($store_invitation->user))
                {
                    $store_invitation->user->workStores()->attach($store_invitation->store, ['created_at'=>now()]);
    
                    $saleperson = $store_invitation->store->roles()->firstOrCreate([
                        'name' => 'salesperson',
                    ]);
        
                    $store_invitation->user->assignRole($saleperson);
    
                }
            }
    
            DB::commit();

            $store_invitation_resource = new StoreInvitationResource($store_invitation);
            $store_invitation_resource->with['message'] = 'Store invitation accepted successfully';
    
            return $store_invitation_resource;

            //code...
        } catch (\Throwable $th) {
            DB::rollBack();
            \Log::Error($th);
            return response()->json([
                "data" => null,
                'status' => 'Failed',
                "message" => $th->getMessage()
            ], 422);
        }
        


    }

     /**
     * Decline the specified resource invitation.
     */
    public function decline(StoreInvitation $store_invitation)
    {
        if(!is_null($store_invitation->accepted_at))
        {

            $store_invitation->status = 'accepted';
            $store_invitation->save();

            return response()->json([
                "data" => null,
                'status' => 'Failed',
                "message" => "Invitation already accepted on ".$store_invitation->accepted_at
            ], 422);
        }

        try {

            DB::beginTransaction();

            if(is_null($store_invitation->declined_at))
            {

                $store_invitation->status = 'declined';
                $store_invitation->declined_at = now();
                $store_invitation->save();
    
                $store_invitation->load(['store','user']);
    
                if(!is_null($store_invitation->user))
                {
                    $store_invitation->user->workStores()->detach($store_invitation->store);
    
                    $saleperson = $store_invitation->store->roles()->firstOrCreate([
                        'name' => 'salesperson',
                    ]);
        
                    $store_invitation->user->revokeRole($saleperson);
    
                }
            }
    
            DB::commit();

            $store_invitation->refresh();

            $store_invitation_resource = new StoreInvitationResource($store_invitation);
            $store_invitation_resource->with['message'] = 'Store invitation declined successfully';
    
            return $store_invitation_resource;

            //code...
        } catch (\Throwable $th) {
            DB::rollBack();
            \Log::Error($th);
            return response()->json([
                "data" => null,
                'status' => 'Failed',
                "message" => $th->getMessage()
            ], 422);
        }
        


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StoreInvitation $store_invitation)
    {
        //
    }
}
