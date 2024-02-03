<?php

namespace App\Http\Requests\Authentication;

use App\Actions\Fortify\PasswordValidationRules;
use App\Models\VerificationCode;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class VerifyOTPRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'otp' => 'required|string|exists:verification_codes,otp',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'otp.exists' => 'The one-time password (OTP) is invalid',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {

        $validator->after(function ($validator) {

            $verification_code = VerificationCode::where('otp', $this->otp)->first();

            $now = Carbon::now();
            if(!is_null($verification_code) && $now->isAfter($verification_code->expire_at))
            {
                $verification_code->delete();

                $validator->errors()->add('otp', 'Your one-time password (OTP) has expired. Please request a new OTP');
            }

        });
    }
}
