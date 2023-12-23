<?php

namespace App\Http\Requests;

use App\Models\Coupon;
use Illuminate\Foundation\Http\FormRequest;
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
            'code' => 'required|string|max:8|unique:coupons,code',
            'discount' => 'required|numeric|min:0.01|max:100',
            'valid_until' => 'required|date|after:'.now(),
            'store_id' => 'nullable|uuid|exists:stores,id',
            'is_bulk_applicable' => 'nullable|boolean',
            'number_of_items' => 'nullable|integer|gt:0',
        ];
    }
}
