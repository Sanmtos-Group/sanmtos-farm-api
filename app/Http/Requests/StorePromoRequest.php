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
            'name' => 'required|string|max:191',
            'description' => 'nullable|string|max:1000',
            'discount' => 'required|numeric|max:99999999',
            'discount_type_id' => 'required|uuid|exists:discount_types,id',
            'requires_min_purchase' => 'sometimes|boolean',
            'min_purchase_price' => 'required_if:requires_min_purchase,true|exclude_unless:requires_min_purchase,true|numeric|gt:0',
            'for_first_purchase_only' => 'sometimes|boolean',
            'free_delivery' => 'sometimes|boolean',
            'free_advert' => 'sometimes|boolean',
            'is_unlimited' => 'sometimes|boolean',
            'start_datetime' => 'required_if:is_unlimited,0,null|date|after_or_equal:'.now(),
            'end_datetime' => 'required_if:is_unlimited,0,null|date|after:start_datetime',
            'store_id' => 'required|uuid|exists:stores,id',
            'image' =>'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', #2mb
        ];
    }
}
