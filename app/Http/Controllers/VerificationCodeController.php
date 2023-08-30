<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\VerificationCode;
use App\Http\Requests\StoreVerificationCodeRequest;
use App\Http\Requests\UpdateVerificationCodeRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class  VerificationCodeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVerificationCodeRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(VerificationCode $verificationCode)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VerificationCode $verificationCode)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVerificationCodeRequest $request, VerificationCode $verificationCode)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VerificationCode $verificationCode)
    {
        //
    }

    public function generate(StoreVerificationCodeRequest $request)
    {
        /**
         *Store Email
         */
        # Store email in database
        $auth = User::create($request->validated());

        # Generate An OTP
        $verificationCode = $this->generateOtp($auth->email);

        $message = "Your OTP To Login is - ".$verificationCode->otp;
        # Return With OTP

        return redirect()->route('otp.verification', ['email' => $verificationCode->email])->with('success',  $message);
    }

    public function generateOtp($email)
    {
        $user = User::where('email', $email)->first();

        # User Does not Have Any Existing OTP
        $verificationCode = VerificationCode::where('email', $email)->latest()->first();

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

    public function verification($email)
    {
        return view('auth.otp-verification')->with([
            'email' => $email
        ]);
    }

    public function loginWithOtp(VerificationCode $verificationCode)
    {
        #Validation
        $verificationCode->validate([
            'email' => 'required|exists:users,email',
            'otp' => 'required'
        ]);

        #Validation Logic
        $code   = VerificationCode::where('email', $verificationCode->email)->where('otp', $verificationCode->otp)->first();

        $now = Carbon::now();
        if (!$code) {
            return redirect()->back()->with('error', 'Your OTP is not correct');
        }elseif($code && $now->isAfter($code->expire_at)){
            return redirect()->route('otp.login')->with('error', 'Your OTP has been expired');
        }

        $user = User::whereId($verificationCode->user_id)->first();

        if($user){
            // Expire The OTP
            $code->update([
                'expire_at' => Carbon::now()
            ]);

            Auth::login($user);

            return redirect('/home');
        }

        return redirect()->route('otp.login')->with('error', 'Your Otp is not correct');
    }
}
