<?php

namespace App\Http\Requests;

use App\Rules\Slug;
use App\Models\Store;
use Illuminate\Foundation\Http\FormRequest;

class StoreStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Store::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:191|unique:stores,name',
            'slug' => ['nullable', 'string','max:191', 'unique:stores,slug', new Slug],
            'email' => 'nullable|string|email|max:191',
            'phone_number' => 'nullable|string|min:10|max:15',
            'description' => 'nullable|string|max:1000',
            'user_id' => 'required|uuid|unique:stores,user_id|exists:users,id'
        ];
    }
}
