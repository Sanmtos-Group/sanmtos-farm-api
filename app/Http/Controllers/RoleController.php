<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Resources\RoleResource;


class RoleController extends Controller
{
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
        $role = Role::create($request->validated());
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
     */
    public function destroy(Role $role)
    {
        $role->delete();
        $role_resource = new RoleResource(null);
        $role_resource->with['message'] = 'Role deleted successfully';
        
        return $role_resource;
    }
}
