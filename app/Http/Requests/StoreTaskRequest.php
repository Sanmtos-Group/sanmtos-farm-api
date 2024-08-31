<?php

namespace App\Http\Requests;

use App\Models\Store;
use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Task::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'operation' => 'sometimes|required|in:insert,edit',
            'comment' => 'required|string|max:191',
            'category_id' => 'required|uuid|exists:categories,id',
            'sub_category_id' => 'nullable|uuid|exists:categories,id',
            'quantity' => 'required|integer|min:1',
            'period' => 'nullable|string|in:day,week,month',
            'duration' => 'nullable|integer|min:1',
            'upload_file' => 'nullable|file|max:2048',
            'assignee_user_id' => 'required|uuid|exists:users,id',
            'status'=> 'nullable|string|in:pending',
            'store_id' => 'sometimes|uuid|exists:stores,id'
        ];
    }

    /**
     * Configure the validator instance.
     * 
     * @overide \Illuminate\Foundation\Http\FormRequest withValidator
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            $store = Store::find($this->store_id);
            if (is_null($store) || is_null($store->staffs()->where('store_user.user_id', $this->assignee_user_id)->first())){
                $validator->errors()->add('assignee_user_id', "The assignee is not a staff of {$store->name} and cannot be assigned a task");
            }
        });
    }
}
