<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Public API
    }

    public function rules(): array
    {
        return [
            'fullName' => ['required', 'string', 'min:2', 'max:255'],
            'mobileNumbers' => ['required', 'array', 'min:1', 'max:5'],
            'mobileNumbers.*' => ['required', 'string', 'regex:/^[0-9+\s\-]{8,20}$/'],
            'cityId' => ['required', 'integer', 'exists:cities,id'],
            'address' => ['required', 'string', 'min:10', 'max:500'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'promoCode' => ['nullable', 'string', 'exists:coupons,code'],
            'cartItems' => ['required', 'array', 'min:1'],
            'cartItems.*.id' => ['required', 'integer', 'exists:products,id'],
            'cartItems.*.quantity' => ['required', 'integer', 'min:1', 'max:100'],
            'subtotal' => ['required', 'numeric', 'min:0'],
            'shipping' => ['required', 'numeric', 'min:0'],
            'total' => ['required', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'in:JOD,USD'],
        ];
    }

    public function messages(): array
    {
        return [
            'fullName.required' => 'Full name is required',
            'mobileNumbers.required' => 'At least one phone number is required',
            'cityId.required' => 'City is required',
            'cityId.exists' => 'Selected city is invalid',
            'address.required' => 'Address is required',
            'cartItems.required' => 'Cart must contain at least one item',
            'cartItems.*.id.exists' => 'One or more products are invalid',
            'subtotal.required' => 'Subtotal is required',
            'total.required' => 'Total is required',
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
