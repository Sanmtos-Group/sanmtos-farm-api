<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use LucasDotVin\Soulbscription\Enums\PeriodicityType;
class UpdatePlanRequest extends FormRequest
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
            'name' => 'string|max:191|'.Rule::unique('plans')->ignore($this->plan),
            'periodicity_type' => 'string|in:'.PeriodicityType::Year.','.PeriodicityType::Month.','.PeriodicityType::Week.','.PeriodicityType::Day.',',
            'periodicity' => 'integer|max:365',
            'grace_days' => 'integer|max:10',
        ];
    }
}
