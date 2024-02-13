<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
            'name' => 'string|min:3|max:191',
            'description' => 'nullable|string|max:1000',
            'short_description' => 'nullable|string|max:191',
            'weight' => 'numeric|min:0.001',
            'volume' => 'numeric|min:0.00',
            'price' => 'numeric|min:0.01',
            'currency' => 'nullable|string',
            'regular_price' => 'nullable|numeric|min:0.01|gt:price',
            'quantity' => 'integer|min:1',
            // 'discount' => 'integer|min:0|max:100',
            'category_id' => 'uuid|exists:categories,id',
            // 'verifier_at' => 'nullable|date',
            // 'verifier_id' => 'nullable|integer|exists:users,id',
        ];
    }
}
