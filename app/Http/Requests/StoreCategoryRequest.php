<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
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
            'name' => 'required|string|max:191|unique:categories,name',
            'description' => 'nullable|string|max:1000',
            'slug' => 'required|string|max:191|unique:categories,slug',
            'parent_category_id'=> 'nullable|numeric|exists:categories,id',
        ];
    }
}
