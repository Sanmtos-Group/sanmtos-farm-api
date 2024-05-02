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
            'name' => 'sometimes|required|string|max:191',
            'description' => 'nullable|string|max:1000',
            'discount' => 'sometimes|required|numeric|max:99999999',
            'discount_type_id' => 'sometimes|required|uuid|exists:discount_types,id',
            'requires_min_purchase' => 'sometimes|boolean',
            'min_purchase_price' => 'required_if:requires_min_purchase,true|exclude_unless:requires_min_purchase,true|numeric|gt:0',
            'for_first_purchase_only' => 'sometimes|boolean',
            'free_delivery' => 'sometimes|boolean',
            'free_advert' => 'sometimes|boolean',
            'is_unlimited' => 'sometimes|boolean',
            'start_datetime' => 'sometimes|required_if:is_unlimited,0,null|date|after_or_equal:'.now(),
            'end_datetime' => 'sometimes|required_if:is_unlimited,0,null|date|after:start_datetime',
            'store_id' => 'nullable|uuid|exists:stores,id',
            'image' =>'sometimes|required|image|mimes:jpeg,png,jpg,gif|max:2048', #2mb
        ];
    }
}
