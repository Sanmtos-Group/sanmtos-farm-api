<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
            'name' => 'required|string|min:3|max:191',
            'description' => 'nullable|string|max:1000',
            'short_description' => 'nullable|string|max:191',
            'price' => 'required|numeric|min:0.01',
            'discount' => 'integer|min:0|max:100',
            'category_id' => 'required|uuid|exists:categories,id',
            'store_id' => 'required|uuid|exists:stores,id',
            'images' =>'array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048', #2mb
            // 'verifier_at' => 'nullable|date',
            // 'verifier_id' => 'nullable|integer|exists:users,id',
        ];
    }
}
