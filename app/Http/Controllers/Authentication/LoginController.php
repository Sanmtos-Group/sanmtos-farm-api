<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use App\Http\Requests\Authentication\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(LoginRequest $request){
        $validate = $request->validated();
        $user = User::where('email', $validate['email'])->first();

        if(Auth::attempt($validate) && $user)
        {
            $request->session()->regenerate();
            return response()->json([
                'access_token' => $user->createToken('api_token')->plainTextToken,
                'data' => $user,
                'token_type' => 'Bearer',
                "message" => "You have successfully logged in!"
            ], 200);
        }

        return response()->json([
            "message" => "The provided credentials do not match in our records."
        ], 401);

    }

}
