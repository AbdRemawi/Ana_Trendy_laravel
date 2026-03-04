<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request for updating an existing inventory transaction.
 *
 * Validates inventory transaction update including quantity and notes.
 * Uses Spatie permission system for authorization.
 */
class UpdateInventoryTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * User must have 'manage products' permission to update inventory transactions.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('manage products') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'quantity' => [
                'required',
                'integer',
                'min:-999999',
                'max:999999',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:1000',
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
            'quantity.required' => __('admin.validation_quantity_required'),
            'quantity.integer' => __('admin.validation_quantity_integer'),
            'quantity.min' => __('admin.validation_quantity_min'),
            'quantity.max' => __('admin.validation_quantity_max'),

            'notes.max' => __('admin.validation_notes_max'),
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
            'quantity' => 'quantity',
            'notes' => 'notes',
        ];
    }
}
