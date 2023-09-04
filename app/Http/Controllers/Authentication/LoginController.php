<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use App\Http\Requests\Authentication\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(LoginRequest $request){
        $validate = $request->validated();

        if(Auth::attempt($validate))
        {
            $request->session()->regenerate();
            return response()->json([
                "message" => "You have successfully logged in!"
            ], 200);
        }

        return response()->json([
            "message" => "The provided credentials do not match in our records."
        ], 401);

    }

}
