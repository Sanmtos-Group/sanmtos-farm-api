<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStoreRequest extends FormRequest
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
            'name' => 'string|min:3|max:191|'.Rule::unique('stores')->ignore($this->store),
            'email' => 'nullable|string|email|max:191',
            'phone_number' => 'nullable|string|min:10|max:15',
            'description' => 'nullable|string|max:1000',
        ];
    }
}
