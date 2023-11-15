<?php

namespace App\Http\Requests;

use App\Models\Promo;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class UpdatePromoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->promo);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'code' => 'string|min:1|max:8|'.Rule::unique('promos')->ignore($this->promo),
            'name' => 'nullable|string|max:191',
            'description' => 'nullable|string|max:1000',
            'discount' => 'numeric|min:0.01|max:100',
            'start_datetime' => 'date',
            'end_datetime' => 'date|after_or_equal:start_datetime',
        ];
    }
}
