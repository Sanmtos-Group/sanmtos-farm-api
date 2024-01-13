<?php

namespace App\Http\Controllers\Authentication;

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
//        $this->createTeam($user->id);

        $token = Str::random(64);

        UserConfirmation::create([
            'user_id' => $user->id,
            'token' => $token
        ]);

        $notify = $user->notify(new NewUserVerification($token));

        $user_resource = new UserResource($user);
        $user_resource->with['message'] = 'User registered successfully. Email verification have been sent to your email';

        return $user_resource;
    }

    public function verifyAccount($token)
    {
        $verifyUser = UserConfirmation::where('token', $token)->first();

        $message = 'Sorry your email cannot be identified.';

        if(!is_null($verifyUser) ){
            $user = $verifyUser->user;

            if(!$user->is_email_verified) {
                $verifyUser->user->is_email_verified = true;
                $verifyUser->user->save();
                $message = "Your e-mail is verified. You can now login.";
            } else {
                $message = "Your e-mail is already verified. You can now login.";
            }
        }else{
            $message = "invalid token.";
        }

        return response()->json([
            'message' => $message,
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
            $verificationCode = $this->generateOtp($user->email);

            $user->notify(new SendLoginOtpCode($verificationCode->otp));

            # Return With OTP Message
            return response()->json([
                "message" => "Your OTP to login has been sent to your email, it will expire in the next 20 minutes"
            ], 201);

        }else {
            # validate data
            $validate = $request->validate([
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            ]);

            # Store email in database
            $user = User::create($validate);

            # Generate An OTP
            $verificationCode = $this->generateOtp($user->email);

            # Send Mail
            $user->notify(new SendLoginOtpCode($verificationCode->otp));

            # Return With OTP Message
            return response()->json([
                "message" => "Your OTP to login has been sent to your email, it will expire in the next 20 minutes"
            ], 201);
        }
    }

    public function generateOtp($email)
    {
        $user = User::where('email', $email)->first();

        # User Does not Have Any Existing OTP
        $verificationCode = VerificationCode::where('user_id', $user->uuid)->latest()->first();

        $now = Carbon::now();

        if($verificationCode && $now->isBefore($verificationCode->expire_at)){
            return $verificationCode;
        }

        # Create a New OTP
        return VerificationCode::create([
            'user_id' => $user->id,
            'otp' => rand(123456, 999999),
            'expire_at' => Carbon::now()->addMinutes(20)
        ]);
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
