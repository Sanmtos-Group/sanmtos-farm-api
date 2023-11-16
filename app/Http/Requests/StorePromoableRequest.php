<?php

namespace App\Http\Requests;

use App\Models\Promo;
use Illuminate\Foundation\Http\FormRequest;

class StorePromoableRequest extends FormRequest
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
            'promo_id' => 'uuid|exists:promos,id',
            'promoable_id' => 'uuid',

            'product_ids' =>'array',
            'product_ids.*' => 'uuid|exists:products,id', 
            'product_id' => 'uuid|exists:products,id',
            
        ];
    }
}
