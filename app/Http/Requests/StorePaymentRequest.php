<?php

namespace App\Http\Requests;

use App\Models\Payment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class StorePaymentRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'nullable|uuid|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'paymentable_id' => 'nullable|uuid',
            'paymentable_type' => 'nullable|string|max:191',
            'gateway' => 'required|'.Rule::in(Payment::GATEWAYS),
            'method' => 'nullable|string|max:191',
            'currency' => 'nullable|string|max:191',
            'ip_address' => 'nullable|ip',
        ];
    }
}
