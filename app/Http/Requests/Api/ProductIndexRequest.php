<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Parameter(
 *     parameter="ProductIndexRequest-brand_id",
 *     name="brand_id",
 *     in="query",
 *     required=false,
 *     @OA\Schema(type="integer")
 * )
 * @OA\Parameter(
 *     parameter="ProductIndexRequest-brand",
 *     name="brand",
 *     in="query",
 *     required=false,
 *     @OA\Schema(type="string")
 * )
 * @OA\Parameter(
 *     parameter="ProductIndexRequest-category_id",
 *     name="category_id",
 *     in="query",
 *     required=false,
 *     @OA\Schema(type="integer")
 * )
 * @OA\Parameter(
 *     parameter="ProductIndexRequest-category",
 *     name="category",
 *     in="query",
 *     required=false,
 *     @OA\Schema(type="string")
 * )
 * @OA\Parameter(
 *     parameter="ProductIndexRequest-size",
 *     name="size",
 *     in="query",
 *     required=false,
 *     @OA\Schema(type="string", enum={"S", "M", "L", "XL", "XXL"})
 * )
 * @OA\Parameter(
 *     parameter="ProductIndexRequest-gender",
 *     name="gender",
 *     in="query",
 *     required=false,
 *     @OA\Schema(type="string", enum={"male", "female", "unisex"})
 * )
 * @OA\Parameter(
 *     parameter="ProductIndexRequest-offers_only",
 *     name="offers_only",
 *     in="query",
 *     required=false,
 *     @OA\Schema(type="boolean")
 * )
 * @OA\Parameter(
 *     parameter="ProductIndexRequest-per_page",
 *     name="per_page",
 *     in="query",
 *     required=false,
 *     @OA\Schema(type="integer", default=15)
 * )
 */
class ProductIndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Brand filtering (single or multiple)
            'brand_id' => ['sometimes', 'integer', 'exists:brands,id'],
            'brand_ids' => ['sometimes', 'array'],
            'brand_ids.*' => ['integer', 'exists:brands,id'],
            'brand' => ['sometimes', 'string', 'exists:brands,slug'],
            'brands' => ['sometimes', 'array'],
            'brands.*' => ['string', 'exists:brands,slug'],

            // Category filtering
            'category_id' => ['sometimes', 'integer', 'exists:categories,id'],
            'category' => ['sometimes', 'string', 'exists:categories,slug'],

            // Price range filtering
            'min_price' => ['sometimes', 'numeric', 'min:0'],
            'max_price' => ['sometimes', 'numeric', 'min:0', 'gte:min_price'],

            // Product attributes
            'size' => ['sometimes', 'string', 'in:S,M,L,XL,XXL'],
            'gender' => ['sometimes', 'string', 'in:male,female,unisex'],

            // Offers only
            'offers_only' => ['sometimes', 'boolean'],

            // Sorting
            'sort' => ['sometimes', 'string', 'in:price_low_high,price_high_low,newest'],

            // Pagination
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'page' => ['sometimes', 'integer', 'min:1'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'brand_id.exists' => 'The selected brand does not exist.',
            'brand_ids.*.exists' => 'One or more selected brands do not exist.',
            'brand.exists' => 'The selected brand slug does not exist.',
            'brands.*.exists' => 'One or more selected brand slugs do not exist.',
            'category_id.exists' => 'The selected category does not exist.',
            'category.exists' => 'The selected category slug does not exist.',
            'min_price.numeric' => 'Minimum price must be a number.',
            'min_price.min' => 'Minimum price cannot be negative.',
            'max_price.numeric' => 'Maximum price must be a number.',
            'max_price.min' => 'Maximum price cannot be negative.',
            'max_price.gte' => 'Maximum price must be greater than or equal to minimum price.',
            'size.in' => 'Size must be one of: S, M, L, XL, XXL.',
            'gender.in' => 'Gender must be one of: male, female, unisex.',
            'sort.in' => 'Sort must be one of: price_low_high, price_high_low, newest.',
            'per_page.min' => 'Cannot show less than 1 item per page.',
            'per_page.max' => 'Cannot show more than 100 items per page.',
            'page.min' => 'Page number must be at least 1.',
        ];
    }

    /**
     * Get per page value with default.
     */
    public function perPage(): int
    {
        return $this->integer('per_page', 15);
    }
}
