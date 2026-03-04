<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\Product;

class CreateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'min:2', 'max:255'],
            'city_id' => ['required', 'integer', 'exists:cities,id'],
            'address' => ['required', 'string', 'min:10'],
            'phone_numbers' => ['required', 'array', 'min:1', 'max:5'],
            'phone_numbers.*' => ['required', 'string', 'regex:/^[0-9+\s\-]{8,20}$/'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:100'],
            'coupon_code' => ['nullable', 'string', 'exists:coupons,code'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.required' => 'Customer name is required.',
            'full_name.min' => 'Customer name must be at least 2 characters.',
            'city_id.required' => 'Please select a city.',
            'city_id.exists' => 'Selected city is invalid.',
            'address.required' => 'Delivery address is required.',
            'address.min' => 'Address must be at least 10 characters.',
            'phone_numbers.required' => 'At least one phone number is required.',
            'phone_numbers.min' => 'At least one phone number is required.',
            'phone_numbers.max' => 'Maximum 5 phone numbers allowed.',
            'phone_numbers.*.regex' => 'Phone number format is invalid.',
            'items.required' => 'Cart must contain at least one item.',
            'items.min' => 'Cart must contain at least one item.',
            'items.*.product_id.exists' => 'One or more products are invalid.',
            'items.*.quantity.min' => 'Quantity must be at least 1.',
            'items.*.quantity.max' => 'Maximum quantity per item is 100.',
            'coupon_code.exists' => 'Invalid coupon code.',
        ];
    }

    /**
     * Configure the validator instance.
     * Add custom validation to check stock availability.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $items = $this->input('items', []);

            foreach ($items as $index => $item) {
                $productId = $item['product_id'] ?? null;
                $requestedQuantity = $item['quantity'] ?? 0;

                if (!$productId || $requestedQuantity <= 0) {
                    continue;
                }

                // Get product with stock quantity
                $product = Product::withStockQuantity()->find($productId);

                if (!$product) {
                    continue; // Will be caught by the exists validator
                }

                $availableStock = (int) $product->stock_quantity;

                // Check if requested quantity exceeds available stock
                if ($requestedQuantity > $availableStock) {
                    $validator->errors()->add("items.{$index}.quantity",
                        "The quantity for '{$product->name}' exceeds available stock. Only {$availableStock} item(s) available."
                    );
                }
            }
        });
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
