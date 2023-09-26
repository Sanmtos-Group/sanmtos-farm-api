<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Resources\PermissionResource;
use App\Http\Resources\RoleResource;


class RoleController extends Controller
{

    /**
     * Create the controller instance.
     */
    public function __construct()
    {
        $this->authorizeResource(Role::class, 'role');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::all();
        $role_resource = new RoleResource($roles);
        return $role_resource;
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
    public function store(StoreRoleRequest $request)
    {
        $validated = $request->validated();

        if(auth()->user()->owns_a_store){
            $validated['store_id'] = auth()->user()->store->id;
        }

        $validated['creator_id'] = auth()->user()->id;
        $role = Role::create($validated);

        $role_resource = new RoleResource($role);
        $role_resource->with['message'] = 'Role created successfully';

        return $role_resource;
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        $role_resource = new RoleResource($role);
        $role_resource->with['message'] = 'Role retrieved successfully';

        return  $role_resource;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, Role $role)
    {
        $role->update($request->validated());
        $role_resource = new RoleResource($role);
        $role_resource->with['message'] = 'Role updated successfully';

        return $role_resource;
    }

    /**
     * Remove the specified resource from storage.
     * 
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Role $role)
    {
        //$this->authorize('delete', $role);

        $role->delete();
        $role_resource = new RoleResource(null);
        $role_resource->with['message'] = 'Role deleted successfully';
        
        return $role_resource;
    }

    /**
     * Display a listing of the resource permissions.
     */
    public function permissions(Role $role)
    {
        $permissions = $role->permissions();
        $permission_resouce = new PermissionResource($permissions);
        $permission_resouce->with['message'] = "{$role->name} permssions retrieved successfully";
        return $permission_resouce;
    }

    /**
     * Grant permission to role.
     */
    public function grantPermission(Role $role, Permission $permission)
    {
        $this->authorize('grant', $permission);

        $role->permissions()->syncWithoutDetaching($permission);

        $role_resource = new RoleResource($role);
        $role_resource->with['message'] = "Granted {$permission->name} permission to {$role->name} successfully";

        return $role_resource;
    }

    /**
     * Revoke permission to role.
     */
    public function revokePermission(Role $role, Permission $permission)
    {
        $role->permissions()->detach($permission->id);

        $role_resource = new RoleResource($role);
        $role_resource->with['message'] = "Revoked {$permission->name} permission for {$role->name} successfully";

        return $role_resource;
    }
}
