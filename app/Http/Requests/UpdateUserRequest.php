<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class UpdateUserRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'string|min:3|max:191',
            'last_name' => 'string|min:3|max:191',
            'gender' => 'string|in:M,F',
            'dialing_code' => 'string|max:4',
            'phone_number' => 'string|min:8|max:20|'.Rule::unique('users')->ignore(auth()->user()->id ?? null),
            'email' => 'email|'.Rule::unique('users')->ignore(auth()->user()->id ?? null),
        ];
    }
}
