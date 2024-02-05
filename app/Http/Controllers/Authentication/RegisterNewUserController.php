<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Requests\Authentication\AccountVerificationRequest;
use App\Http\Requests\Authentication\SendOTPRequest;
use App\Http\Requests\Authentication\ResendCodeRequest;
use App\Http\Requests\Authentication\SendPasswordResetRequest;
use App\Models\User;
use App\Models\Team;
use App\Http\Controllers\Controller;
use App\Http\Requests\Authentication\RegisterNewUserRequest;
use App\Http\Resources\UserResource;
use App\Models\UserConfirmation;
use App\Models\VerificationCode;
use App\Notifications\NewUserVerification;
use App\Notifications\SendLoginOtpCode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegisterNewUserController extends Controller
{
    public function register(RegisterNewUserRequest $request){
        $validation = $request->validated();
        $user = User::create([
            "first_name" => $validation['first_name'],
            "last_name" => $validation['last_name'],
            "email" => $validation['email'],
            "gender" => $validation['gender'] ?? null,
            "dialing_code" => $validation['dialing_code'],
            "phone_number" => $validation['phone_number'],
            "password" => Hash::make($validation['password'])
        ]);
        // $this->createTeam($user->id);

        $OTP = $user->generateOTP();
         $notify = $user->notify(new NewUserVerification($OTP));

        $user_resource = new UserResource($user);
        $user_resource->with['message'] = 'User registered successfully. Email verification have been sent to your email';

        return $user_resource;
    }

    public function verifyAccount(AccountVerificationRequest $request)
    {
        $validated = $request->validated();

        $verification_code = VerificationCode::where('otp', $validated['otp'])->first();
        $user = $verification_code->user;

        $user->is_email_verified = true;
        $user->email_verified_at = now();
        $user->save();

        $verification_code->delete();

        return response()->json([
            'status' => "OK",
            'message' => "Your e-mail is already verified. You can now login.",
        ], 200);

    }

    public function registerWithOnlyPhoneNumber(Request $request){

    }

    /**
     * Passwordless Login
     */

    public function registerWithOnlyEmail(Request $request){

        #If user exist
        $user   = User::where('email', $request->email)->first();

        if ($user){
            # Generate An OTP
            $OTP= $user->generateOTP();

            $user->notify(new SendLoginOtpCode($OTP));

            # Return With OTP Message
            return response()->json([
                "message" => "Your OTP to login has been sent to your email, it will expire in the next one hour"
            ], 201);

        }else {
            # validate data
            $validate = $request->validate([
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            ]);

            # Store email in database
            $user = User::create($validate);

            # Generate An OTP
            $OTP = $user->generateOTP();

            # Send Mail
            $user->notify(new SendLoginOtpCode($OTP));

            # Return With OTP Message
            return response()->json([
                "message" => "Your OTP to login has been sent to your email, it will expire in the next one hour"
            ], 201);
        }
    }

    public function resend(SendOTPRequest $request)
    {
        $validate = $request->validated();
        $user = User::where('email', $validate['email'])->first();

        $user->verificationCodes()->delete();
        $OTP = $user->generateOTP();

        $user->notify(new SendLoginOtpCode($OTP));

        return response()->json([
            "Status"=> "OK",
            "message" => "A one-time password (OTP) has been sent to your email address. This OTP is valid for the next hour."
        ], 201);
    }

    /**
     * Create a personal team for the user.
     */
    protected function createTeam(User $user): void
    {
        $user->ownedTeams()->save(Team::forceCreate([
            'user_id' => $user->uuid,
            'name' => explode(' ', $user->name, 2)[0]."'s Team",
            'personal_team' => true,
        ]));
    }

}
