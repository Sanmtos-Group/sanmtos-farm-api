<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
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
            'comment' => 'required|string|max|191',
            'category_id' => 'required|uuid|exists:categories,id',
            'sub_category_id' => 'required|uuid|exists:categories,id',
            'quantity' => 'nullable|numeric',
            'period' => 'nullable|string|in:day,week,month',
            'upload_file' => 'nullable|file|max:2048',
            'assignee_user_id' => 'required|uuid|exists:users,id',
            'status'=> 'nullable|string|in:pending',
            'store_id' => 'sometimes|uuid|exists:stores,id'
        ];
    }
}
