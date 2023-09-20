<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Role;

class StoreRoleRequest extends FormRequest
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
            'name' => 'required|string|max:191|',
            'description' => 'nullable|string|max:1000',
            'store_id' => 'nullable|uuid|exists:stores,id'
        ];
    }

    /**
     * Configure the validator instance.
     * 
     * @overide \Illuminate\Foundation\Http\FormRequest withValidator
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $role = Role::where('name', $this->name)->where('store_id', $this->store_id)->first();
            if (!is_null($role)){
                $validator->errors()->add('name', 'The role already exist');
            }
        });
    }

}
