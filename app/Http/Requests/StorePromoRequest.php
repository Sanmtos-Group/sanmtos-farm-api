<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePromoRequest extends FormRequest
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
            'name' => 'required|string|max:191',
            'description' => 'required|string|max:1000',
            'discount' => 'integer|min:0|max:100',
            'is_universal' => 'boolean',
            'start_time' => 'required|date',
            'end_time' => 'required|date',
            'store_id' => 'uuid|exists:stores,id'
        ];
    }
}
