<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use App\Http\Requests\Authentication\PasswordResetRequest;
use App\Http\Requests\Authentication\SendPasswordResetRequest;
use App\Http\Requests\Authentication\OTPRequest;
use App\Notifications\PasswordResetNotification;
use App\Models\User;
use App\Models\VerificationCode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordResetController extends Controller
{
    /**
     * Send Pass
     */
    public function sendPasswordResetCode(SendPasswordResetRequest $request)
    {
        $validated = $request->validated();

        $user = User::where('email', $validated['email'])->first();
        $OTP = $user->generateOTP();

        $user->notify(new PasswordResetNotification($OTP)); 

        return response()->json([
            "Status" => "OK",
            "message" => "A one-time password (OTP) for password reset has been sent to your email address. This OTP is valid for the next hour."
        ], 201);       
    }

    public function verifyOTP(OTPRequest $request)
    {
        $validated = $request->validated();
        $verification_code = VerificationCode::where('otp', $validated['otp'])->first();

        return response()->json([
            "data" => $verification_code->only('otp'),
            "Status" => "OK",
            "message" => "The one-time password (OTP) is valid"
        ], 200);      
    }

    public function resendCode(OTPRequest $request)
    {
        $validated = $request->validated();
        $verification_code = VerificationCode::where('otp', $validated['otp'])->first();
        $user = $verification_code->user;

        $OTP = $user->generateOTP();

        $user->notify(new PasswordResetNotification($OTP)); 

        return response()->json([
            "Status" => "OK",
            "message" => "A one-time password (OTP) for password reset has been sent to your email address. This OTP is valid for the next hour."
        ], 201);   
    }

    public function resetPassword(PasswordResetRequest $request)
    {
        $validated = $request->validated();
        $verification_code = VerificationCode::where('otp', $validated['otp'])->first();
        $user = $verification_code->user;

        $user->update([
            "password" => Hash::make($validated['new_password'])
        ]);

        $verification_code->delete();

        return response()->json([
            "Status" => "OK",
            "message" => "Password reset successfully",
        ], 201);
    }

}