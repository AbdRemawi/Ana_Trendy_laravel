<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

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
        // Skip rows whose file is missing so the storefront never renders a broken
        // <img>. Clean the data up permanently with: php artisan products:prune-missing-images
        return $this->images
            ->filter(fn ($image) => $this->imageFileExists($image))
            ->map(function ($image) {
                return [
                    'id' => $image->id,
                    'url' => $image->image_url,
                    'is_primary' => (bool) $image->is_primary,
                ];
            })
            ->values()
            ->toArray();
    }

    /**
     * Whether the image's underlying file actually exists on the public disk.
     */
    private function imageFileExists($image): bool
    {
        $path = (string) $image->image_path;

        return $path !== ''
            && ! str_contains($path, 'undefined')
            && Storage::disk('public')->exists($path);
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
            // Only consider images whose file actually exists, preferring the primary,
            // so a missing primary file falls back to a real one instead of breaking.
            $usable = $this->images->filter(fn ($image) => $this->imageFileExists($image));

            $primaryImage = $usable->firstWhere('is_primary', true) ?? $usable->first();

            return $primaryImage?->image_url;
        }

        return null;
    }
}
