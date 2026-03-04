<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Form request for storing a new permission.
 *
 * Validates permission name and ensures consistent snake_case formatting.
 * Authorization is handled via PermissionPolicy.
 */
class StorePermissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * User must have 'manage permissions' permission to create permissions.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('manage permissions') ?? false;
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
                'max:100',
                'regex:/^[a-z][a-z0-9_]*(\s+[a-z][a-z0-9_]*)*$/',
                Rule::unique('permissions', 'name'),
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
            'name.required' => 'The permission name is required.',
            'name.min' => 'The permission name must be at least 2 characters.',
            'name.max' => 'The permission name may not be greater than 100 characters.',
            'name.regex' => 'The permission name must use snake_case format (lowercase letters, numbers, underscores, spaces).',
            'name.unique' => 'A permission with this name already exists.',
        ];
    }

    /**
     * Prepare the data for validation.
     * Convert the permission name to snake_case if spaces are used.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => str()->snake($this->name),
        ]);
    }
}
