<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Form request for updating an existing product.
 *
 * Validates product update data including brand, category, name,
 * description, size, gender, prices, status, and images.
 * Uses Spatie permission system for authorization.
 *
 * ENHANCED: Now includes image upload with primary image validation.
 * - Images are optional on update (existing images are preserved)
 * - If new images are uploaded, exactly one must be marked as primary
 * - Existing images can be managed via separate UI controls
 */
class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * User must have 'manage products' permission to update products.
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
                Rule::unique('products', 'sku')->ignore($this->route('product')),
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
            // Product Images - Optional on update
            'images' => [
                'nullable',
                'array',
                'max:10',
            ],
            'images.*' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:51200', // 50MB per image (will be compressed to 2MB)
            ],
            // Primary image selection - single image ID from all images (existing + new)
            'primary_image_id' => [
                'nullable',
                'integer',
            ],
            // Images to remove
            'remove_images' => [
                'nullable',
                'array',
            ],
            'remove_images.*' => [
                'nullable',
                'integer',
            ],
        ];
    }

    /**
     * Configure the validator instance.
     *
     * Add custom after-validation hook to ensure:
     * - Product has at least one image after update
     * - Primary image ID is valid (belongs to this product and not being removed)
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $newImages = $this->file('images', []);
            $primaryImageId = $this->input('primary_image_id');
            $removeImages = $this->input('remove_images', []);
            $product = $this->route('product');

            // Ensure product will have at least one image after update
            $currentImageCount = $product->images()->count();
            $removingCount = count(array_filter($removeImages, fn($id) => !empty($id)));
            $addingCount = count($newImages);

            if (($currentImageCount - $removingCount + $addingCount) === 0) {
                $validator->errors()->add('images', __('admin.validation_images_required'));
                return;
            }

            // Validate primary_image_id if provided
            if ($primaryImageId) {
                // Check if the primary image is being removed
                if (in_array($primaryImageId, $removeImages)) {
                    $validator->errors()->add('primary_image_id', __('admin.validation_primary_image_removed'));
                    return;
                }

                // Check if the primary image belongs to this product
                $imageExists = \App\Models\ProductImage::where('product_id', $product->id)
                    ->where('id', $primaryImageId)
                    ->exists();

                if (!$imageExists) {
                    // If it's not an existing image, it might be a new image
                    // New images won't have IDs yet, so we'll validate the index
                    $maxNewIndex = count($newImages) - 1;
                    if ($primaryImageId > $maxNewIndex) {
                        $validator->errors()->add('primary_image_id', __('admin.validation_primary_image_invalid'));
                    }
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
            'sku.required' => __('admin.validation_sku_required'),
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

            'images.array' => __('admin.validation_images_array'),
            'images.min' => __('admin.validation_images_min'),
            'images.max' => __('admin.validation_images_max'),

            'images.*.image' => __('admin.validation_images_image'),
            'images.*.mimes' => __('admin.validation_images_mimes'),
            'images.*.max' => __('admin.validation_images_max_file'),

            'primary_image_id.integer' => __('admin.validation_primary_image_id_integer'),
            'primary_image_id.exists' => __('admin.validation_primary_image_id_exists'),

            'remove_images.*.integer' => __('admin.validation_remove_images_integer'),
            'remove_images.*.exists' => __('admin.validation_remove_images_exists'),
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
            'images' => __('admin.attribute_images'),
            'primary_image_id' => __('admin.attribute_primary_image'),
            'remove_images' => __('admin.attribute_remove_images'),
        ];
    }
}
