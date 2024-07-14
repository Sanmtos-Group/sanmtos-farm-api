<?php

namespace App\Http\Requests;

use App\Models\StoreInvitation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
class StoreStoreInvitationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', StoreInvitation::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'store_id' => 'required|uuid|exists:stores,id',
            'roles' => 'nullable|string|max:191',
        ];
    }
    /**
     * Get the "after" validation callables for the request.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) 
            {
                $store_invitation = StoreInvitation::where('email', $this->email)
                ->where('store_id', $this->store_id)->first();

                if(!is_null($store_invitation))
                {
                    $error_message = 'Store invitation already sent, awaiting response';

                    $validator->errors()->add('store_invitation_error',$error_message);
                }        
            }
        ];
    }
}
