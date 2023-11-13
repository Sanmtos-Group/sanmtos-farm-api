<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCouponRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', $this->coupon);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'code' => 'required|string|max:8|unique:coupon,code',
            'discount' => 'required|numeric|min:0.01|max:100',
            'start_datetime' => 'required|date|after_or_equal:'.now(),
            'valid_until' => 'required|date|after_or_equal:start_datetime',
        ];
    }
}
