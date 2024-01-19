<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use App\Http\Requests\Authentication\PasswordResetCodeRequest;
use App\Http\Requests\Authentication\PasswordResetRequest;
use App\Http\Requests\Authentication\ResendCodeRequest;
use App\Http\Requests\Authentication\SendPasswordResetRequest;
use App\Notifications\PasswordResetNotification;
use App\Models\User;
use App\Models\VerificationCode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordResetController extends Controller
{
    public function sendPasswordResetCode(SendPasswordResetRequest $request)
    {
        $validated = $request->validated();

        $user = User::where('email', $validated['email'])->first();
        $OTP = $user->generateOTP();

        $user->notify(new PasswordResetNotification($OTP)); 

        return response()->json([
            "message" => "Your OTP to reset password has been sent to your email, it will expire in the next one hour"
        ], 201);       
    }

    public function verifyOtp(PasswordResetCodeRequest $request)
    {
        $validated = $request->validated();
        $verification_code = VerificationCode::where('otp', $validated['otp'])->first();

        $now = now();

        if ($verification_code && $now->isAfter($verification_code->expire_at)){
            return response()->json([
                'message' => "The password reset code has expired",
            ], 200);
        }
        elseif(!is_null($verification_code) ){
            return response()->json([
                'message' => "Success",
            ], 200);
        }else{
            return response()->json([
                'message' => "invalid token.",
            ], 419);
        }
    }

    public function resendCode(ResendCodeRequest $request){
        $validated = $request->validated();
        $verification_code = VerificationCode::where('otp', $validated['otp'])->first();

        if(!is_null($verification_code) ){
            $user = User::where('id', $verification_code->user_id)->first();

            if (!is_null($user)) {
                $otp = rand(123456, 999999);

                $verification_code->update([
                    'otp' => $otp,
                    'expire_at' => Carbon::now()->addHours()
                ]);

                $user->notify(new SendPasswordResetRequest($otp));  //Here is a bug

                return response()->json([
                    "message" => "Code sent, it will expire in the next one hour"
                ], 201);
            }
        }else{
            return response()->json([
                'message' => "Your email can not be verify, input a correct email in the forgot password page",
            ], 200);
        }

        return "Error code";
    }

    public function resetPassword(PasswordResetRequest $request)
    {
        $validated = $request->validated();
        $code = VerificationCode::where('otp', $validated['otp'])->first();

        if (!is_null($code)){
            $user = User::where('id', $code->user_id)->first();

            $user->update([
                "password" => Hash::make($validated['new_password'])
            ]);

            $code->delete();

            return response()->json([
                "message" => "Password retrieved successfully"
            ], 201);
        }
        return "Something went wrong";
    }

}
