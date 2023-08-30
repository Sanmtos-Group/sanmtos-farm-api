<?php

namespace App\Http\Requests\Authentication;

use App\Actions\Fortify\PasswordValidationRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Laravel\Jetstream\Jetstream;

class RegisterNewUserRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => '   ',
            'last_name' => 'required|string|max:191',
            'dialing_code' => 'required|string|max:4',
            'phone_number' => 'required|string|max:191|'.Rule::unique('users')->ignore($this->phone_number),
            'email' => 'required|string|email|max:191|'.Rule::unique('users')->ignore($this->email),
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ];
    }
}
