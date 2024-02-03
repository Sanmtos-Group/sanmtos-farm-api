<?php

namespace App\Http\Requests\Authentication;

use App\Actions\Fortify\PasswordValidationRules;
use App\Models\VerificationCode;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
class PasswordResetRequest extends FormRequest
{
    use PasswordValidationRules;

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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'otp' => 'required|string|exists:verification_codes,otp',
            "new_password" => $this->passwordRules(),
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

}
