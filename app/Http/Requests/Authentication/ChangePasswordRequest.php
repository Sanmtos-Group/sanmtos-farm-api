<?php

namespace App\Http\Requests\Authentication;

use App\Actions\Fortify\PasswordValidationRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class ChangePasswordRequest extends FormRequest
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
            'password' => 'required|string',
            'new_password' => $this->passwordRules(),
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
            
            $user = auth()->user();

            if(is_null($user)) 
            {
                $validator->errors()->add('unauthenticated', 'Please login to change your password');
            }
            elseif (!Hash::check($this->password, $user->password))
            {

                $validator->errors()->add('password', 'Incorrect password');

            }
          
        });
    }

}
