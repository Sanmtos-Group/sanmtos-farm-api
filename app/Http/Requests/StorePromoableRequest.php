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
            
            'product_ids' =>'sometimes|array',
            'product_ids.*' => 'sometimes|uuid|exists:products,id', 
            'product_id' => 'sometimes|uuid|exists:products,id',

            'category_ids' =>'sometimes|array',
            'category_ids.*' => 'sometimes|uuid|exists:categories,id', 
            'category_id' => 'somtimes|uuid|exists:categories,id',

            'recipient_ids' =>'sometimes|array',
            'recipient_ids.*' => 'sometimes|uuid|exists:users,id',
            'recipient_id' => 'sometimes|uuid|exists:users,id',
            
        ];
    }
}
