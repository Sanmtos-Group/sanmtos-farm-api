<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use LucasDotVin\Soulbscription\Enums\PeriodicityType;
class StorePlanRequest extends FormRequest
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
            'name' => 'required|string|max:191|unique:plans,name',
            'periodicity_type' => 'required|string|in:'.PeriodicityType::Year.','.PeriodicityType::Month.','.PeriodicityType::Week.','.PeriodicityType::Day.',',
            'periodicity' => 'required|integer|max:365',
            'grace_days' => 'integer|max:10',
        ];
    }
}
