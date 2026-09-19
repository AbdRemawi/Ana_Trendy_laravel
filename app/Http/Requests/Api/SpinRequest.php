<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SpinRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Public API
    }

    public function rules(): array
    {
        return [
            // Opaque per-browser identifier generated & stored on the front-end.
            'device_id' => ['required', 'string', 'min:8', 'max:100'],
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
