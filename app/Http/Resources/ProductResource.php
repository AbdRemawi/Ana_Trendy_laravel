<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

/**
 * @mixin \App\Models\Product
 */
class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Determine effective price (offer_price if exists and is less than sale_price, else sale_price)
        $hasOffer = $this->offer_price !== null && $this->offer_price < $this->sale_price;
        $effectivePrice = $hasOffer ? (float) $this->offer_price : (float) $this->sale_price;

        // Get stock status
        $stockQuantity = (int) ($this->stock_quantity ?? 0);
        $stockStatus = $stockQuantity > 0 ? 'in_stock' : 'out_of_stock';

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'short_description' => $this->getShortDescription(),

            // Brand
            'brand' => $this->whenLoaded('brand', fn() => [
                'name' => $this->brand->name,
                'slug' => $this->brand->slug,
            ]),

            // Main image
            'main_image' => $this->getMainImageUrl(),

            // Pricing
            'price' => $effectivePrice,
            'old_price' => $hasOffer ? (float) $this->sale_price : null,
            'has_offer' => $hasOffer,

            // Stock
            'stock_quantity' => $stockQuantity,
            'stock_status' => $stockStatus,
            'max_orderable_quantity' => $stockQuantity,
        ];
    }

    /**
     * Get short description (truncated).
     */
    private function getShortDescription(): string
    {
        if (empty($this->description)) {
            return '';
        }

        // Strip tags and truncate to 100 characters
        $clean = strip_tags($this->description);
        return Str::limit($clean, 100, '');
    }

    /**
     * Get the main image URL.
     *
     * @return string|null
     */
    private function getMainImageUrl(): ?string
    {
        // Check if primaryImage relation is loaded
        if ($this->relationLoaded('primaryImage') && $this->primaryImage->isNotEmpty()) {
            return $this->primaryImage->first()?->image_url;
        }

        // Check if images relation is loaded
        if ($this->relationLoaded('images') && $this->images->isNotEmpty()) {
            // Try to get primary image
            $primaryImage = $this->images->firstWhere('is_primary', true);

            // Fall back to first image
            if (!$primaryImage) {
                $primaryImage = $this->images->first();
            }

            return $primaryImage?->image_url;
        }

        return null;
    }

    /**
     * Get human-readable stock status.
     *
     * @param int $stockQuantity
     * @return string
     */
    private function getStockStatus(int $stockQuantity): string
    {
        return match (true) {
            $stockQuantity <= 0 => 'out_of_stock',
            $stockQuantity <= 5 => 'low_stock',
            $stockQuantity <= 20 => 'medium_stock',
            default => 'in_stock',
        };
    }
}
