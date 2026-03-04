<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Base Form Request
 *
 * Provides common validation rules and patterns for all form requests.
 * Reduces duplication by centralizing reusable validation logic.
 *
 * Child classes should override:
 * - authorize() with specific permission check
 * - rules() with field-specific rules
 */
abstract class BaseFormRequest extends FormRequest
{
    /**
     * Common validation rules for standard fields.
     * Can be used in child class rules() method.
     */
    protected function nameRules(?string $table = null, ?string $column = 'name', $ignore = null): array
    {
        $rules = [
            'required',
            'string',
            'max:255',
        ];

        if ($table) {
            $rule = Rule::unique($table, $column);

            if ($ignore) {
                $rule->ignore($ignore);
            }

            $rules[] = $rule;
        }

        return $rules;
    }

    /**
     * Standard image upload validation rules.
     */
    protected function imageRules(bool $required = false, int $maxSizeKB = 2048, string $mimes = 'jpeg,png,jpg,webp'): array
    {
        return array_filter([
            $required ? 'required' : 'nullable',
            'image',
            "mimes:{$mimes}",
            "max:{$maxSizeKB}",
        ]);
    }

    /**
     * Standard status field validation rules.
     */
    protected function statusRules(bool $required = true): array
    {
        return array_filter([
            $required ? 'required' : 'nullable',
            'in:active,inactive',
        ]);
    }

    /**
     * Standard email validation rules.
     */
    protected function emailRules(?string $table = null, $ignore = null): array
    {
        $rules = [
            'required',
            'string',
            'email:rfc,dns',
            'max:255',
        ];

        if ($table) {
            $rule = Rule::unique($table, 'email');

            if ($ignore) {
                $rule->ignore($ignore);
            }

            $rules[] = $rule;
        }

        return $rules;
    }

    /**
     * Standard password validation rules.
     */
    protected function passwordRules(bool $required = true): array
    {
        return array_filter([
            $required ? 'required' : 'nullable',
            'string',
            'min:8',
            'confirmed',
        ]);
    }

    /**
     * Standard phone number validation rules.
     */
    protected function phoneRules(bool $required = false): array
    {
        return array_filter([
            $required ? 'required' : 'nullable',
            'string',
            'max:20',
            'regex:/^[+]?[\d\s\-\(\)]+$/',
        ]);
    }

    /**
     * Standard numeric field validation (e.g., price).
     */
    protected function priceRules(bool $required = true, $min = 0): array
    {
        return array_filter([
            $required ? 'required' : 'nullable',
            'numeric',
            "min:{$min}",
        ]);
    }

    /**
     * Common error messages for validation rules.
     * Child classes can merge these with their own messages().
     */
    public function commonMessages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'name.unique' => 'This name already exists.',

            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'This email already exists.',

            'password.required' => 'The password field is required.',
            'password.min' => 'The password must be at least 8 characters.',
            'password.confirmed' => 'The password confirmation does not match.',

            'image.image' => 'The file must be an image.',
            'image.mimes' => 'The image must be a file of type: jpeg, png, jpg, webp.',
            'image.max' => 'The image may not be greater than 2MB.',

            'status.required' => 'The status field is required.',
            'status.in' => 'The selected status is invalid. Must be active or inactive.',

            'price.required' => 'The price field is required.',
            'price.numeric' => 'The price must be a number.',
            'price.min' => 'The price must be at least :min.',

            'phone.regex' => 'The phone number format is invalid.',
            'phone.max' => 'The phone number may not be greater than 20 characters.',
        ];
    }

    /**
     * Common attribute names for better error messages.
     */
    public function commonAttributes(): array
    {
        return [
            'name' => 'name',
            'email' => 'email address',
            'password' => 'password',
            'password_confirmation' => 'password confirmation',
            'phone' => 'phone number',
            'status' => 'status',
            'image' => 'image',
            'logo' => 'logo',
        ];
    }
}
