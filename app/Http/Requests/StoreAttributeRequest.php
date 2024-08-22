<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttributeRequest extends FormRequest
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
            'name' => 'required|string|max:191|unique:attributes,name',
            'slug' => 'nullable|string|max:191|unique:attributes,slug',

            'category_ids' =>'sometimes|array',
            'category_ids.*' => 'uuid|exists:categories,id', 
      ];
    }
}
