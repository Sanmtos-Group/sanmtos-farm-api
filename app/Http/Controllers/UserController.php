<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;

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
}
