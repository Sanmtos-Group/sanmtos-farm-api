<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttributableRequest extends FormRequest
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
            'category_ids' =>'sometimes|array',
            'category_ids.*' => 'sometimes|uuid|exists:categories,id', 
            'category_id' => 'sometimes|uuid|exists:categories,id',
        ];
    }
}
