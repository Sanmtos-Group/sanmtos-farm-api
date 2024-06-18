<?php

namespace App\Http\Requests;

use App\Models\Coupon;
use App\Rules\MinWords;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class UpdateCouponRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->coupon);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'code' => 'sometimes|required|string|'.Rule::unique('coupons')->ignore($this->coupon),
            'description' => ['nullable','string','max:191', new MinWords(2)],
            'discount_type_id' => 'sometimes|uuid|exists:discount_types,id',
            'discount' => 'sometimes|numeric|gt:0',
            'requires_min_purchase' => 'sometimes|boolean',
            'min_purchase_price' => 'required_if:requires_min_purchase,true|exclude_unless:requires_min_purchase,true|numeric|gt:0',
            'for_first_purchase_only' => 'sometimes|boolean',
            // 'max_usage' => 'sometimes|integer|gt:0',
            // 'unlimited_usuage' => 'sometimes|boolean',
            'expiration_date' => 'sometimes|date|after:'.now(),
            'store_id' => 'nullable|uuid|exists:stores,id',

            'recipient_ids' =>'sometimes|array',
            'recipient_ids.*' => 'uuid|exists:users,id',

            'applicable_product_ids' =>'sometimes|array',
            'applicable_product_ids.*' => 'uuid|exists:products,id', 

            'applicable_category_ids' =>'sometimes|array',
            'applicable_category_ids.*' => 'uuid|exists:categories,id', 
        ];
    }
}
