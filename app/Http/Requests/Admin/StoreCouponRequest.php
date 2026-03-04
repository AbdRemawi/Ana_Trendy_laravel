<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreCouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => [
                'required',
                'string',
                'max:50',
                'unique:coupons,code',
                'regex:/^[A-Z0-9_]+$/i',
            ],
            'type' => [
                'required',
                'in:fixed,percentage,free_delivery',
            ],
            'value' => [
                'required',
                'numeric',
                'min:0',
                $this->type === 'percentage' ? 'max:100' : '',
            ],
            'minimum_order_amount' => [
                'required',
                'numeric',
                'min:0',
            ],
            'max_uses' => [
                'nullable',
                'integer',
                'min:1',
            ],
            'valid_from' => [
                'required',
                'date',
                'after:now',
            ],
            'valid_until' => [
                'nullable',
                'date',
                'after:valid_from',
            ],
            'is_active' => [
                'boolean',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => __('admin.validation_code_required'),
            'code.unique' => __('admin.validation_code_unique'),
            'code.max' => __('admin.validation_code_max'),
            'code.regex' => __('admin.validation_code_format'),
            'type.required' => __('admin.validation_type_required'),
            'type.in' => __('admin.validation_type_in'),
            'value.required' => __('admin.validation_value_required'),
            'value.numeric' => __('admin.validation_value_numeric'),
            'value.min' => __('admin.validation_value_min'),
            'value.max' => __('admin.validation_value_max_percentage'),
            'minimum_order_amount.required' => __('admin.validation_minimum_order_required'),
            'minimum_order_amount.numeric' => __('admin.validation_minimum_order_numeric'),
            'minimum_order_amount.min' => __('admin.validation_minimum_order_min'),
            'max_uses.integer' => __('admin.validation_max_uses_integer'),
            'max_uses.min' => __('admin.validation_max_uses_min'),
            'valid_from.required' => __('admin.validation_valid_from_required'),
            'valid_from.date' => __('admin.validation_valid_from_date'),
            'valid_from.after' => __('admin.validation_valid_from_future'),
            'valid_until.after' => __('admin.validation_valid_until_after'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422)
        );
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'is_active' => $this->has('is_active') ? true : false,
        ]);
    }
}
