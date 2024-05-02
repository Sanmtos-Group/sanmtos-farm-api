<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();

        $request->session()->invalidate();

        return response()->json([
            "message" => "Bye for now! We're expecting you soon",
        ], 200);
    }
}
