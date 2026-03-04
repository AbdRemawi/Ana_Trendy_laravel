<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Form request for storing a new city.
 *
 * Validates city creation data including name and status.
 * Uses Spatie permission system for authorization.
 */
class StoreCityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * User must have 'manage cities' permission to create cities.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('manage cities') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * Name: Required, unique (only for active cities), max 255 characters
     * Status: Required, must be active or inactive
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('cities', 'name')->where(function ($query) {
                    return $query->where('is_active', true);
                }),
            ],
            'is_active' => [
                'required',
                'boolean',
            ],
        ];
    }

    /**
     * Get custom validation error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => __('admin.validation_city_name_required'),
            'name.max' => __('admin.validation_city_name_max'),
            'name.unique' => __('admin.validation_city_name_unique'),
            'is_active.required' => __('admin.validation_is_active_required'),
            'is_active.boolean' => __('admin.validation_is_active_boolean'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => __('admin.city_name'),
            'is_active' => __('admin.city_status'),
        ];
    }
}
