<?php

namespace App\Http\Controllers\Authentication;

use App\Models\User;
use App\Models\Team;
use App\Http\Controllers\Controller;
use App\Http\Requests\Authentication\RegisterNewUserRequest;
use App\Http\Resources\UserResource;
use App\Models\VerificationCode;
use App\Notifications\SendLoginOtpCode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterNewUserController extends Controller
{
    public function register(RegisterNewUserRequest $request){
        $validation = $request->validated();
        $user = User::create([
            "first_name" => $validation['first_name'],
            "last_name" => $validation['last_name'],
            "email" => $validation['email'],
            "dialing_code" => $validation['dialing_code'],
            "phone_number" => $validation['phone_number'],
            "password" => Hash::make($validation['password'])
        ]);
//        $this->createTeam($user->id);

        $user_resource = new UserResource($user);
        $user_resource->with['message'] = 'User registered successfully. Please proceed to login';

        return $user_resource;
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
            $auth = User::create($validate);

            # Generate An OTP
            $verificationCode = $this->generateOtp($auth->email);

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

        // Create a New OTP
        return VerificationCode::create([
            'user_id' => $user->id,
            'otp' => rand(123456, 999999),
            'expire_at' => Carbon::now()->addMinutes(20)
        ]);
    }

    public function loginWithOtp(Request $request)
    {
        #Validation
        $request->validate([
            'otp' => 'required'
        ]);

        #Validation Logic
        $verificationCode   = VerificationCode::where('otp', $request->otp)->first();

        $now = Carbon::now();
        if (!$verificationCode) {
            return response()->json([
                "message" => "Your OTP is not correct",
            ], 401);
        }elseif($verificationCode && $now->isAfter($verificationCode->expire_at)){
            return response()->json([
                "message" => "Your OTP has been expired",
            ], 401);
        }

        $user = User::whereId($verificationCode->user_id)->first();

        if($user){
            // Expire The OTP
            $verificationCode->delete();

            Auth::login($user);

            $request->session()->regenerate();
            return response()->json([
                'access_token' => $user->createToken('api_token')->plainTextToken,
                'token_type' => 'Bearer',
                "message" => "You have successfully logged in!"
            ], 200);

        }

        return response()->json([
            "message" => "Your Otp is not correct",
        ], 401);

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
