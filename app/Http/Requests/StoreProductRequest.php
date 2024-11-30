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
            'weight' => 'required|numeric|min:0.001',
            'length' => 'numeric|min:0.00',
            'width' => 'numeric|min:0.00',
            'height' => 'numeric|min:0.00',
            'shelf_life' => 'nullable|string|max:191',
            'volume' => 'numeric|min:0.00',
            'price' => 'required|numeric|min:0.01',
            'quantity' => 'integer|min:1',
            // 'regular_price' => 'required|numeric|min:0.01',
            // 'discount' => 'integer|min:0|max:100',
            'category_id' => 'required|exists:categories,id',
            'store_id' => 'required|exists:stores,id',
            'images' =>'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048', #2mb
            // 'verifier_at' => 'nullable|date',
            // 'verifier_id' => 'nullable|integer|exists:users,id',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        if(auth()->check()){
            $user =  auth()->user();
            $this->merge([
                'store_id' => $user->ownsAStore ? $user->store->id : $this->store_id
            ]);
        }

        $this->merge([
            'currency' => 'NGN',
        ]);
    }
    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'weight' => 'weight (KG)',
        ];
    }
}
