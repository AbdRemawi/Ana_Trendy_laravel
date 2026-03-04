<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AssignCourierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'delivery_courier_id' => ['required', 'integer', 'exists:delivery_couriers,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'delivery_courier_id.required' => __('admin.validation_courier_id_required'),
            'delivery_courier_id.exists' => __('admin.validation_courier_id_exists'),
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
}
