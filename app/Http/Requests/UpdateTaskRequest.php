<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->task);;
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
            'comment' => 'sometimes|required|string|max:191',
            'category_id' => 'sometimes|required|uuid|exists:categories,id',
            'sub_category_id' => 'nullable|uuid|exists:categories,id',
            'quantity' => 'sometimes|required|integer|min:1',
            'period' => 'nullable|string|in:day,week,month',
            'duration' => 'nullable|integer|min:1',
            'upload_file' => 'nullable|file|max:2048',
            'assignee_user_id' => 'sometimes|required|uuid|exists:users,id',
            'status'=> 'sometimes|required|string|in:pending,ongoing,done',
            'store_id' => 'sometimes|uuid|exists:stores,id'
        ];
    }
}
