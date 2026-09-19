<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ClaimSpinRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Public API
    }

    public function rules(): array
    {
        return [
            // Token returned by the /spin endpoint for this specific spin.
            'token' => ['required', 'uuid', 'exists:spinner_spins,token'],
            // The phone number the customer types in the win modal.
            'phone_number' => ['required', 'string', 'regex:/^[0-9+\s\-]{8,20}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'token.exists' => 'Spin not found.',
            'phone_number.regex' => 'Please enter a valid phone number.',
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
