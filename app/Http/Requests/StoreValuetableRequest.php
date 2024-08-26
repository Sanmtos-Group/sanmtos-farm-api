<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreValuetableRequest extends FormRequest
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
            'value_ids' =>'sometimes|array',
            'value_ids.*' => 'sometimes|uuid|exists:values,id', 
            'value_id' => 'sometimes|uuid|exists:values,id',

            'values' =>'sometimes|array',
            'values.*' => 'sometimes|required|string', 
            'value' => 'sometimes|required|string',
        ];
    }
}
