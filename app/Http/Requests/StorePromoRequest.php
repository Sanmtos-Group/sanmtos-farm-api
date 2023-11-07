<?php

namespace App\Http\Requests;

use App\Models\Promo;
use Illuminate\Foundation\Http\FormRequest;

class StorePromoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Promo::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'code' => 'required|string|max:191|unique:promos,code',
            'name' => 'nullable|string|max:191',
            'description' => 'nullable|string|max:1000',
            'discount' => 'required|numeric|min:0.01|max:100',
            'start_datetime' => 'required|date|after_or_equal:'.now(),
            'end_datetime' => 'required|date|after:start_datetime',
            'promoable_id' => 'uuid',
            'promoable_type' => 'string|max:191',
        ];
    }
}
