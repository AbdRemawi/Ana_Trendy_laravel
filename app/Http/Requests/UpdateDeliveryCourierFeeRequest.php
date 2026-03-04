<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Form request for updating an existing delivery courier fee.
 *
 * Validates courier fee update with strict business rules:
 * - Only ONE fee allowed per courier-city combination
 * - Money must be >= 0
 * - Courier and City must exist and be active
 * - Decimal precision correct (3 decimal places)
 * - Editing must respect unique constraint (exclude current record)
 *
 * Uses Spatie permission system for authorization.
 */
class UpdateDeliveryCourierFeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * User must have 'manage delivery courier fees' permission to update fees.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('manage delivery courier fees') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * Delivery Courier ID: Required, must exist in delivery_couriers table, must be active
     * City ID: Required, must exist in cities table, must be active
     * Real Fee Amount: Required, decimal, >= 0, max 10 digits with 3 decimal places
     * Display Fee Amount: Required, decimal, >= 0, max 10 digits with 3 decimal places
     * Currency: Required, max 3 characters (ISO 4217 currency code)
     * Is Active: Required, boolean
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $fee = $this->route('fee');

        return [
            'delivery_courier_id' => [
                'required',
                'integer',
                Rule::exists('delivery_couriers', 'id')->where(function ($query) {
                    return $query->where('is_active', true);
                }),
            ],
            'city_id' => [
                'required',
                'integer',
                Rule::exists('cities', 'id')->where(function ($query) {
                    return $query->where('is_active', true);
                }),
            ],
            'real_fee_amount' => [
                'required',
                'numeric',
                'decimal:0,3',
                'min:0',
                'max:99999999.999',
            ],
            'display_fee_amount' => [
                'required',
                'numeric',
                'decimal:0,3',
                'min:0',
                'max:99999999.999',
            ],
            'currency' => [
                'required',
                'string',
                'max:3',
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
            'delivery_courier_id.required' => __('admin.validation_courier_id_required'),
            'delivery_courier_id.integer' => __('admin.validation_courier_id_integer'),
            'delivery_courier_id.exists' => __('admin.validation_courier_id_exists'),
            'city_id.required' => __('admin.validation_city_id_required'),
            'city_id.integer' => __('admin.validation_city_id_integer'),
            'city_id.exists' => __('admin.validation_city_id_exists'),
            'real_fee_amount.required' => __('admin.validation_real_fee_amount_required'),
            'real_fee_amount.numeric' => __('admin.validation_real_fee_amount_numeric'),
            'real_fee_amount.decimal' => __('admin.validation_real_fee_amount_decimal'),
            'real_fee_amount.min' => __('admin.validation_real_fee_amount_min'),
            'real_fee_amount.max' => __('admin.validation_real_fee_amount_max'),
            'display_fee_amount.required' => __('admin.validation_display_fee_amount_required'),
            'display_fee_amount.numeric' => __('admin.validation_display_fee_amount_numeric'),
            'display_fee_amount.decimal' => __('admin.validation_display_fee_amount_decimal'),
            'display_fee_amount.min' => __('admin.validation_display_fee_amount_min'),
            'display_fee_amount.max' => __('admin.validation_display_fee_amount_max'),
            'currency.required' => __('admin.validation_currency_required'),
            'currency.max' => __('admin.validation_currency_max'),
            'is_active.required' => __('admin.validation_is_active_required'),
            'is_active.boolean' => __('admin.validation_is_active_boolean'),
        ];
    }

    /**
     * Configure the validator instance.
     *
     * Adds custom validation to ensure only ONE fee record per courier/city combination.
     * Excludes the current fee record being updated from the check.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $courierId = $this->input('delivery_courier_id');
            $cityId = $this->input('city_id');
            $fee = $this->route('fee');

            // Check if a fee record already exists for this courier/city combination
            // Exclude the current fee record being updated
            $existingFee = \App\Models\DeliveryCourierFee::query()
                ->where('delivery_courier_id', $courierId)
                ->where('city_id', $cityId)
                ->where('id', '!=', $fee?->id)
                ->first();

            if ($existingFee) {
                $validator->errors()->add('delivery_courier_id', __('admin.validation_courier_city_combination_unique'));
            }
        });
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'delivery_courier_id' => __('admin.courier'),
            'city_id' => __('admin.city'),
            'real_fee_amount' => __('admin.real_fee_amount'),
            'display_fee_amount' => __('admin.display_fee_amount'),
            'currency' => __('admin.currency'),
            'is_active' => __('admin.fee_status'),
        ];
    }
}
