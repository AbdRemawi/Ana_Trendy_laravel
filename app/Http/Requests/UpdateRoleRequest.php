<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

/**
 * Form request for updating an existing role.
 *
 * Validates role name updates and permission synchronization.
 * The role being updated is passed via route model binding.
 */
class UpdateRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * User must have 'manage roles' permission to update roles.
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
        $roleId = $this->route('role')?->id;

        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'min:2',
                'max:50',
                'regex:/^[a-z0-9_]+$/',
                Rule::unique('roles', 'name')->ignore($roleId),
            ],
            'permissions' => [
                'sometimes',
                'array',
            ],
            'permissions.*' => [
                'string',
                'exists:permissions,name',
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
            'permissions.array' => __('admin.validation_permissions_array'),
            'permissions.*.exists' => __('admin.validation_permissions_exists'),
        ];
    }
}
