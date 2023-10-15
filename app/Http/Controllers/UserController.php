<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Http\Resources\RoleResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        $user_resource = new UserResource($users);
        return $user_resource;
    }

    /**
     * Display authenticated user profile
     */
    public function profile(Request $request)
    {
        $user = $request->user();
        $user_resource = new UserResource($user);
        $user_resource->with['message'] = 'User profile retrieved successfully';

        return $user_resource;
    }


    /**
     * Display a listing users that are staff.
     */
    public function staffs()
    {
        $users = User::where('is_staff', true)->get();
        $user_resource = new UserResource($users);

        $user_resource->with['message'] = 'Sanmtos staffs retrieved successfully';

        return $user_resource;
    }


    /**
     * Display a listing users that are store staff.
     * 
     */
    public function storeStaffs()
    {
        $users = User::has('workStores')->get();

        $user_resource = new UserResource($users);

        $user_resource->with['message'] = 'Stores staffs retrieved successfully';

        return $user_resource;

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }

    /**
     * Display a listing of the resource roles.
     */
    public function roles(User $user)
    {
        $roles = $user->roles();
        $roles_resouce = new RoleResource($roles);
        $roles_resouce->with['message'] = "{$user->first_name}'s roles retrieved successfully";
        return $roles_resouce;
    }

    /**
     * Assign role to user.
     */
    public function assignRole(User $user, Role $role)
    {
        $this->authorize('assign', [$role, $user]);

        $user->roles()->syncWithoutDetaching($role);


        $role_resource = new RoleResource($user->roles);
        $role_resource->with['message'] = "Assigned {$role->name} to {$user->first_name} successfully";

        return $role_resource;
    }

    /**
     * Remove use role.
     * 
     * @param App\Models\User $user
     * @param App\Models\Role $role
     */
    public function removeRole(User $user, Role $role)
    {
        $this->authorize('remove', [$role, $user]);

        $user->roles()->detach($role->id);

        $role_resource = new RoleResource($user->roles);
        $role_resource->with['message'] = "Remove {$role->name} from {$user->name} successfully";

        return $role_resource;
    }
}
