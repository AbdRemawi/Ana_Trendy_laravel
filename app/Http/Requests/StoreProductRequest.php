<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Form request for storing a new product.
 *
 * Validates product creation data including brand, category, name,
 * description, size, gender, prices, status, and images.
 * Uses Spatie permission system for authorization.
 *
 * ENHANCED: Now includes image upload with primary image validation.
 * - At least one image is required
 * - Exactly one image must be marked as primary
 */
class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * User must have 'manage products' permission to create products.
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
            'sku' => [
                'nullable',
                'string',
                'max:50',
                'unique:products,sku',
            ],
            'brand_id' => [
                'required',
                'integer',
                Rule::exists('brands', 'id'),
            ],
            'category_id' => [
                'required',
                'integer',
                Rule::exists('categories', 'id'),
            ],
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'description' => [
                'nullable',
                'string',
                'max:5000',
            ],
            'size' => [
                'nullable',
                'in:S,M,L,XL,XXL',
            ],
            'gender' => [
                'required',
                'in:male,female,unisex',
            ],
            'cost_price' => [
                'required',
                'decimal:0,2',
                'min:0',
                'max:99999999.99',
            ],
            'sale_price' => [
                'required',
                'decimal:0,2',
                'min:0',
                'max:99999999.99',
                'gt:cost_price',
            ],
            'offer_price' => [
                'nullable',
                'decimal:0,2',
                'min:0',
                'max:99999999.99',
                'lt:sale_price',
            ],
            'status' => [
                'required',
                'in:active,inactive',
            ],
            // Initial Stock Quantity - Required for product creation
            'initial_quantity' => [
                'required',
                'integer',
                'min:0',
            ],
            // Product Images - Upload multiple images (first image auto-becomes primary)
            'images' => [
                'required',
                'array',
                'min:1',
                'max:10',
            ],
            'images.*' => [
                'required',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:51200', // 50MB per image (will be compressed to 2MB)
            ],
        ];
    }

    /**
     * Configure the validator instance.
     *
     * Add custom after-validation hook to ensure images are valid.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $images = $this->file('images', []);

            // Ensure at least one image is uploaded
            if (empty($images) || count($images) === 0) {
                $validator->errors()->add('images', __('admin.validation_images_required'));
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
            'sku.unique' => __('admin.validation_sku_unique'),
            'sku.max' => __('admin.validation_sku_max'),

            'brand_id.required' => __('admin.validation_brand_required'),
            'brand_id.exists' => __('admin.validation_brand_exists'),

            'category_id.required' => __('admin.validation_category_required'),
            'category_id.exists' => __('admin.validation_category_exists'),

            'name.required' => __('admin.validation_name_required'),
            'name.max' => __('admin.validation_name_max'),

            'description.max' => __('admin.validation_description_max'),

            'size.in' => __('admin.validation_size_in'),

            'gender.required' => __('admin.validation_gender_required'),
            'gender.in' => __('admin.validation_gender_in'),

            'cost_price.required' => __('admin.validation_cost_price_required'),
            'cost_price.decimal' => __('admin.validation_cost_price_decimal'),
            'cost_price.min' => __('admin.validation_cost_price_min'),
            'cost_price.max' => __('admin.validation_cost_price_max'),

            'sale_price.required' => __('admin.validation_sale_price_required'),
            'sale_price.decimal' => __('admin.validation_sale_price_decimal'),
            'sale_price.min' => __('admin.validation_sale_price_min'),
            'sale_price.max' => __('admin.validation_sale_price_max'),
            'sale_price.gt' => __('admin.validation_sale_price_gt'),

            'offer_price.decimal' => __('admin.validation_offer_price_decimal'),
            'offer_price.min' => __('admin.validation_offer_price_min'),
            'offer_price.max' => __('admin.validation_offer_price_max'),
            'offer_price.lt' => __('admin.validation_offer_price_lt'),

            'status.required' => __('admin.validation_status_required'),
            'status.in' => __('admin.validation_status_in'),

            'initial_quantity.required' => __('admin.validation_initial_quantity_required'),
            'initial_quantity.integer' => __('admin.validation_initial_quantity_integer'),
            'initial_quantity.min' => __('admin.validation_initial_quantity_min'),

            'images.required' => __('admin.validation_images_required'),
            'images.array' => __('admin.validation_images_array'),
            'images.min' => __('admin.validation_images_min'),
            'images.max' => __('admin.validation_images_max'),

            'images.*.required' => __('admin.validation_images_required_file'),
            'images.*.image' => __('admin.validation_images_image'),
            'images.*.mimes' => __('admin.validation_images_mimes'),
            'images.*.max' => __('admin.validation_images_max_file'),
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
            'sku' => __('admin.attribute_sku'),
            'brand_id' => __('admin.attribute_brand'),
            'category_id' => __('admin.attribute_category'),
            'name' => __('admin.attribute_product_name'),
            'description' => __('admin.attribute_description'),
            'size' => __('admin.attribute_size'),
            'gender' => __('admin.attribute_gender'),
            'cost_price' => __('admin.attribute_cost_price'),
            'sale_price' => __('admin.attribute_sale_price'),
            'offer_price' => __('admin.attribute_offer_price'),
            'status' => __('admin.attribute_status'),
            'initial_quantity' => __('admin.attribute_initial_stock_quantity'),
            'images' => __('admin.attribute_images'),
        ];
    }
}
