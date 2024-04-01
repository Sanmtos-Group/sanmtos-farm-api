<?php

namespace App\Http\Requests;

use App\Models\Coupon;
use Illuminate\Foundation\Http\FormRequest;

class StoreCouponableRequest extends FormRequest
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
    
            'product_ids' =>'array',
            'product_ids.*' => 'uuid|exists:products,id', 
            'product_id' => 'uuid|exists:products,id',

            'category_ids' =>'array',
            'category_ids.*' => 'uuid|exists:categories,id', 
            'category_id' => 'uuid|exists:categories,id',
        ];
    }
}
