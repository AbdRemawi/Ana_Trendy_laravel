<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

/**
 * Form request for updating an existing user.
 *
 * Validates user update data including mobile number format,
 * email uniqueness (excluding current user), optional password,
 * and role assignment. Uses Spatie permission system for role validation.
 */
class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * User must have 'manage users' permission to update users.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('manage users') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * Mobile format: Must be exactly 10 digits starting with 078, 079, or 077
     * Password: Optional on update (nullable), minimum 8 characters if provided
     * Email: Nullable but unique if provided (excluding current user)
     * Role: Must exist in roles table (Spatie permission system)
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('user')?->id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'mobile' => [
                'required',
                'string',
                'regex:/^(078|079|077)\d{7}$/',
                Rule::unique('users', 'mobile')->ignore($userId),
            ],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'password' => [
                'nullable',
                'string',
                'min:8',
                Password::defaults(),
            ],
            'role' => [
                'required',
                'string',
                'exists:roles,name',
            ],
            'status' => [
                'required',
                'in:active,inactive,suspended',
            ],
            'commission_rate' => [
                'nullable',
                'required_if:role,affiliate',
                'numeric',
                'min:0',
                'max:100',
            ],
        ];
    }

    /**
     * Get custom validation error messages.
     *
     * Uses translation keys for i18n support.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => __('admin.validation_name_required'),
            'name.max' => __('admin.validation_name_max'),

            'mobile.required' => __('admin.mobile_help'),
            'mobile.regex' => __('admin.mobile_help'),
            'mobile.unique' => __('admin.mobile_unique'),

            'email.email' => __('admin.validation_email_email'),
            'email.unique' => __('admin.email_unique'),

            'password.min' => __('admin.password_min'),

            'role.required' => __('admin.validation_role_required'),
            'role.exists' => __('admin.validation_role_exists'),

            'status.required' => __('admin.validation_status_required'),
            'status.in' => __('admin.validation_status_in'),

            'commission_rate.numeric' => __('admin.validation_commission_numeric'),
            'commission_rate.min' => __('admin.validation_commission_min'),
            'commission_rate.max' => __('admin.validation_commission_max'),
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
            'name' => 'name',
            'mobile' => 'mobile number',
            'email' => 'email',
            'password' => 'password',
            'role' => 'role',
            'status' => 'status',
            'commission_rate' => 'commission rate',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * Remove password from request if it's empty to prevent
     * updating with an empty string.
     */
    protected function prepareForValidation(): void
    {
        if ($this->password === '') {
            $this->merge(['password' => null]);
        }
    }
}
