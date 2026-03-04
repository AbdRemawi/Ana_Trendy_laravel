<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Form request for storing a new role.
 *
 * Validates role name and ensures the requesting user has permission
 * to create roles (checked via policy/authorize method).
 */
class StoreRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * User must have 'manage roles' permission to create roles.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('manage roles') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:2',
                'max:50',
                'regex:/^[a-z0-9_]+$/',
                Rule::unique('roles', 'name'),
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
            'name.required' => __('admin.validation_name_required'),
            'name.min' => __('admin.validation_name_min'),
            'name.max' => __('admin.validation_name_max'),
            'name.regex' => __('admin.validation_name_regex'),
            'name.unique' => __('admin.validation_name_unique'),
        ];
    }
}
