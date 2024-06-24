<?php

namespace App\Http\Requests;

use App\Models\Coupon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validtion\Rule;
use App\Rules\MinWords;
class StoreCouponRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Coupon::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'code' => 'required|string|unique:coupons,code',
            'description' => ['nullable','string','max:191', new MinWords(2)],
            'discount_type_id' => 'required|uuid|exists:discount_types,id',
            'discount' => 'required|numeric|gt:0',
            'requires_min_purchase' => 'sometimes|boolean',
            'min_purchase_price' => 'required_if:requires_min_purchase,true|exclude_unless:requires_min_purchase,true|numeric|gt:0',
            'for_first_purchase_only' => 'sometimes|boolean',
            // 'max_usage' => 'sometimes|integer|gt:0',
            // 'unlimited_usuage' => 'sometimes|boolean',
            'expiration_date' => 'required|date|after:'.now(),
            'store_id' => 'required|uuid|exists:stores,id',

            'recipient_ids' =>'sometimes|array',
            'recipient_ids.*' => 'uuid|exists:users,id',

            'applicable_product_ids' =>'sometimes|array',
            'applicable_product_ids.*' => 'uuid|exists:products,id', 

            'applicable_category_ids' =>'sometimes|array',
            'applicable_category_ids.*' => 'uuid|exists:categories,id', 

        ];
        
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'min_purchase_price.required_if' => 'The min purchase price field is required when requires min purchase is true',
        ];
    }
}
