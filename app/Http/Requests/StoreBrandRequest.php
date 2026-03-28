<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Form request for storing a new brand.
 *
 * Validates brand creation data including name, slug (auto-generated),
 * logo upload, and status. Uses Spatie permission system for authorization.
 */
class StoreBrandRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * User must have 'manage brands' permission to create brands.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('manage brands') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * Name: Required, unique, max 255 characters
     * Slug: Unique, auto-generated from name on backend
     * Logo: Optional, must be an image (jpeg, png, jpg, webp), max 2MB
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
                Rule::unique('brands', 'name'),
            ],
            'logo' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:51200', // 50MB (will be compressed to 2MB)
            ],
            'status' => [
                'required',
                'in:active,inactive',
            ],
        ];
    }

    /**
     * Get custom validation error messages.
     *
     * Provides Arabic-friendly messages for key validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'name.unique' => 'This brand name already exists.',

            'logo.image' => 'The logo must be an image.',
            'logo.mimes' => 'The logo must be a file of type: jpeg, png, jpg, webp.',
            'logo.max' => 'The logo may not be greater than 50MB.',

            'status.required' => 'The status field is required.',
            'status.in' => 'The selected status is invalid.',
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
            'name' => 'brand name',
            'logo' => 'brand logo',
            'status' => 'status',
        ];
    }
}
