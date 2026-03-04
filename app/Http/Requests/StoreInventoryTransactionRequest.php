<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Form request for storing a new inventory transaction.
 *
 * CRITICAL: Enforces that inventory quantity NEVER goes below zero.
 * Validation fails BEFORE database write if resulting inventory < 0.
 *
 * Uses Spatie permission system for authorization.
 */
class StoreInventoryTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * User must have 'manage products' permission to create inventory transactions.
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
            'product_id' => [
                'required',
                'integer',
                Rule::exists('products', 'id'),
            ],
            'type' => [
                'required',
                'in:supply,sale,return,damage,adjustment',
            ],
            // For supply/return/adjustment: can be positive or negative (adjustment only)
            // For sale/damage: must be positive (these decrease stock)
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
     * Configure the validator instance.
     *
     * CRITICAL VALIDATION: Prevents negative inventory BEFORE database write.
     * - Stock must ALWAYS be >= 0
     * - No "temporary negative" allowed
     * - No lazy logic
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $productId = (int) $this->input('product_id');
            $type = $this->input('type');
            $quantity = (int) $this->input('quantity', 0);

            $product = \App\Models\Product::find($productId);

            if (!$product) {
                return;
            }

            // Supply and return should have positive quantities
            if (in_array($type, ['supply', 'return']) && $quantity <= 0) {
                $validator->errors()->add('quantity', __('admin.quantity_positive_for_type', ['type' => $type]));
                return;
            }

            // Sale and damage should have positive quantities (they reduce stock)
            if (in_array($type, ['sale', 'damage']) && $quantity <= 0) {
                $validator->errors()->add('quantity', __('admin.quantity_positive_for_type', ['type' => $type]));
                return;
            }

            // CRITICAL: Check if transaction would result in negative stock
            if (!$product->canApplyTransaction($type, $quantity)) {
                $currentStock = $product->stock_quantity;
                $projectedStock = $product->calculateProjectedStock($type, $quantity);

                $validator->errors()->add('quantity', __(
                    'admin.inventory_would_go_negative',
                    [
                        'current_stock' => $currentStock,
                        'projected_stock' => $projectedStock,
                        'requested_quantity' => $quantity,
                        'type' => $type,
                    ]
                ));
            }

            // Additional validation for adjustment type (can be positive or negative)
            if ($type === 'adjustment') {
                $currentStock = $product->stock_quantity;
                $projectedStock = $currentStock + $quantity;

                if ($projectedStock < 0) {
                    $validator->errors()->add('quantity', __(
                        'admin.adjustment_would_go_negative',
                        [
                            'current_stock' => $currentStock,
                            'adjustment_value' => $quantity,
                        ]
                    ));
                }
            }
        });
    }

    /**
     * Get custom validation error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'product_id.required' => __('admin.validation_product_required'),
            'product_id.exists' => __('admin.validation_product_exists'),

            'type.required' => __('admin.validation_type_required'),
            'type.in' => __('admin.validation_type_in'),

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
            'product_id' => __('admin.attribute_product'),
            'type' => __('admin.attribute_transaction_type'),
            'quantity' => __('admin.attribute_quantity'),
            'notes' => __('admin.attribute_notes'),
        ];
    }
}
