<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Product
 */
class ProductDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => [
                'id' => $this->id,
                'name' => $this->name,
                'slug' => $this->slug,
                'description' => $this->description ?? '',

                'brand' => $this->whenLoaded('brand', fn() => [
                    'id' => $this->brand->id,
                    'name' => $this->brand->name,
                    'slug' => $this->brand->slug,
                ]),

                'category' => $this->whenLoaded('category', fn() => [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                    'slug' => $this->category->slug,
                ]),

                'size' => $this->size,
                'gender' => $this->gender,

                'primary_image' => $this->getPrimaryImageUrl(),

                'images' => $this->whenLoaded('images', fn() => $this->transformImages()),

                'pricing' => $this->calculatePricing(),

                'stock' => $this->calculateStock(),
            ],
        ];
    }

    /**
     * Transform images collection.
     *
     * @return array<int, array<string, mixed>>
     */
    private function transformImages(): array
    {
        return $this->images->map(function ($image) {
            return [
                'id' => $image->id,
                'url' => $image->image_url,
                'is_primary' => (bool) $image->is_primary,
            ];
        })->toArray();
    }

    /**
     * Calculate pricing details.
     *
     * @return array<string, mixed>
     */
    private function calculatePricing(): array
    {
        $regularPrice = (float) $this->sale_price;
        $offerPrice = $this->offer_price ? (float) $this->offer_price : null;
        $hasOffer = $offerPrice !== null && $offerPrice < $regularPrice;
        $effectivePrice = $hasOffer ? $offerPrice : $regularPrice;

        $discountPercentage = null;
        if ($hasOffer && $regularPrice > 0) {
            $discountPercentage = round(($regularPrice - $offerPrice) / $regularPrice * 100, 1);
        }

        return [
            'regular' => $regularPrice,
            'effective' => $effectivePrice,
            'has_offer' => $hasOffer,
            'discount_percentage' => $discountPercentage,
        ];
    }

    /**
     * Calculate stock details.
     *
     * @return array<string, mixed>
     */
    private function calculateStock(): array
    {
        $quantity = (int) ($this->stock_quantity ?? 0);
        $lowStockThreshold = 5;

        return [
            'status' => $quantity > 0 ? 'in_stock' : 'out_of_stock',
            'quantity' => $quantity,
            'is_low_stock' => $quantity > 0 && $quantity <= $lowStockThreshold,
            'max_orderable_quantity' => $quantity,
        ];
    }

    /**
     * Get primary image URL.
     *
     * @return string|null
     */
    private function getPrimaryImageUrl(): ?string
    {
        if ($this->relationLoaded('images') && $this->images->isNotEmpty()) {
            $primaryImage = $this->images->firstWhere('is_primary', true);

            if (!$primaryImage) {
                $primaryImage = $this->images->first();
            }

            return $primaryImage?->image_url;
        }

        return null;
    }
}
