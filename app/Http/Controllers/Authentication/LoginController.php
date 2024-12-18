<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use App\Http\Requests\Authentication\LoginRequest;
use App\Http\Requests\Authentication\VerifyOTPRequest;
use App\Models\User;
use App\Models\VerificationCode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function login(LoginRequest $request)
    {
        $validate = $request->validated();
        $user = User::where('email', $validate['email'])->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials do not match in our records.'],
            ]);
        }

        $request->session()->regenerate();
        
        return response()->json([
            'access_token' => $user->createToken('api_token')->plainTextToken,
            'data' => $user,
            'token_type' => 'Bearer',
            "message" => "You have successfully logged in!"
        ], 200);
    }

    /**
     * Login a user via OTP
     * 
     * @method POST
     * @param App\Http\Requests\Authentication\LoginViaVerifyOTPRequest $request 
     */
    public function loginViaOTP(VerifyOTPRequest $request)
    {
        $validated = $request->validated();
        $verification_code = VerificationCode::where('otp', $validated['otp'])->first();
        $user = $verification_code->user?? null; 

        if (!$user) {
            throw ValidationException::withMessages([
                'otp' => ['The invalid OTP.'],
            ]);
        }
        
        $verification_code->delete();
        
        $request->session()->regenerate();

        return response()->json([
            'access_token' => $user->createToken('api_token')->plainTextToken,
            'data' => $user,
            'token_type' => 'Bearer',
            "message" => "You have successfully logged in!"
        ], 200);
    }

}
