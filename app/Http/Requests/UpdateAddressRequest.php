<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
        // return $this->user()->can('update', $this->address); // this is commented out because it address in route and request conflict
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'nullable|string|max:191',
            'first_name' => 'nullable|string|min:3|max:191',
            'last_name' => 'nullable|string|min:3|max:191',
            'dialing_code' => 'nullable|string|min:1|max:4',
            'phone_number' => 'nullable|string|min:6|max:15',
            'address' => 'sometimes|required|string|min:3|max:191',
            'zip_code' => 'nullable|string|max:10',
            'country_id' => 'sometimes|required|uuid|exists:countries,id',
            'state' => 'sometimes|required|string|max:191',
            'lga' => 'nullable|string|max:191',
            'is_preferred' => 'boolean',  
        ];
    }

}
