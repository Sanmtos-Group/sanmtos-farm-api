<?php

namespace App\Http\Controllers\Authentication;

use App\Models\User;
use App\Models\Team;
use App\Http\Controllers\Controller;
use App\Http\Requests\Authentication\RegisterNewUserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class RegisterNewUserController extends Controller
{
    public function register(RegisterNewUserRequest $request){
        $user = User::create($request->validated());
        $this->createTeam($user);

        $user_resource = new UserResource($user);
        $user_resource->with['message'] = 'User registered successfully. Please proceed to login';

        return $user_resource;
    }

    public function registerWithOnlyPhoneNumber(Request $request){
        
    }

    public function registerWithOnlyEmail(Request $request){
        
    }

    /** 
     * Create a personal team for the user.
     */
    protected function createTeam(User $user): void
    {
        $user->ownedTeams()->save(Team::forceCreate([
            'user_id' => $user->id,
            'name' => explode(' ', $user->name, 2)[0]."'s Team",
            'personal_team' => true,
        ]));
    }
}