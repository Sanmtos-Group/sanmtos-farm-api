<?php

namespace App\Http\Requests;

use App\Models\Promo;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'code' => 'required|string|max:191|unique',
            'name' => 'nullable|string|max:191',
            'description' => 'nullable|string|max:1000',
            'discount_amount' => 'numeric|min:0.01',
            'discount_percent' => 'integer|min:1|max:100|'.Rule::requiredIf(is_null($this->discount_amount)),
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date',
        ];
    }
}
