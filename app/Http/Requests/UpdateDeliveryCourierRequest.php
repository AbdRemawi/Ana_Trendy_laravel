<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Form request for updating an existing delivery courier.
 *
 * Validates delivery courier update data including name, contact phone, and status.
 * Uses Spatie permission system for authorization.
 * Excludes current courier from unique validation.
 */
class UpdateDeliveryCourierRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * User must have 'manage delivery couriers' permission to update delivery couriers.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('manage delivery couriers') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * Name: Required, unique (only for active couriers, excluding current), max 255 characters
     * Contact Phone: Optional, max 20 characters
     * Status: Required, must be active or inactive
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $courier = $this->route('courier');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('delivery_couriers', 'name')
                    ->where(function ($query) {
                        return $query->where('is_active', true);
                    })
                    ->ignore($courier?->id),
            ],
            'contact_phone' => [
                'nullable',
                'string',
                'max:20',
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
            'name.required' => __('admin.validation_courier_name_required'),
            'name.max' => __('admin.validation_courier_name_max'),
            'name.unique' => __('admin.validation_courier_name_unique'),
            'contact_phone.max' => __('admin.validation_contact_phone_max'),
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
            'name' => __('admin.courier_name'),
            'contact_phone' => __('admin.courier_contact_phone'),
            'is_active' => __('admin.courier_status'),
        ];
    }
}
