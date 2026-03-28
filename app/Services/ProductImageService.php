<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Product Image Service
 *
 * Handles all product image-related business logic:
 * - Image upload and storage with compression
 * - Primary image management
 * - Image ordering
 * - Image deletion
 *
 * Extracted from ProductController for better separation of concerns.
 */
class ProductImageService
{
    private ImageCompressorService $compressor;

    public function __construct(ImageCompressorService $compressor)
    {
        $this->compressor = $compressor;
    }
    /**
     * Upload new images for a product.
     *
     * @param Product $product
     * @param array $images Array of UploadedFile objects
     * @return array Array of created ProductImage models
     */
    public function uploadImages(Product $product, array $images): array
    {
        $uploadedImages = [];
        $maxSortOrder = $product->images()->max('sort_order') ?? 0;

        foreach ($images as $index => $image) {
            // Use compressor service to compress and store the image
            $path = $this->compressor->compressAndStore($image, 'products', 'public');

            $uploadedImages[] = ProductImage::create([
                'product_id' => $product->id,
                'image_path' => $path,
                'is_primary' => false,
                'sort_order' => ++$maxSortOrder,
            ]);
        }

        return $uploadedImages;
    }

    /**
     * Set an image as primary.
     *
     * Ensures only ONE primary image exists per product.
     *
     * @param Product $product
     * @param int $imageId
     * @return void
     */
    public function setPrimaryImage(Product $product, int $imageId): void
    {
        DB::transaction(function () use ($product, $imageId) {
            // First, ensure ALL images are not primary
            ProductImage::where('product_id', $product->id)
                ->update(['is_primary' => false]);

            // Then set the selected image as primary
            ProductImage::where('product_id', $product->id)
                ->where('id', $imageId)
                ->update(['is_primary' => true]);
        });
    }

    /**
     * Remove specified images from a product.
     *
     * @param Product $product
     * @param array $imageIds
     * @return void
     */
    public function removeImages(Product $product, array $imageIds): void
    {
        foreach ($imageIds as $imageId) {
            if (empty($imageId)) continue;

            $image = ProductImage::find($imageId);
            if ($image && $image->product_id === $product->id) {
                Storage::disk('public')->delete($image->image_path);
                $image->delete();
            }
        }
    }

    /**
     * Ensure at least one primary image exists for a product.
     *
     * If no primary image exists, sets the first image as primary.
     *
     * @param Product $product
     * @return void
     */
    public function ensurePrimaryImageExists(Product $product): void
    {
        $primaryCount = $product->images()->where('is_primary', true)->count();

        if ($primaryCount === 0) {
            $firstImage = $product->images()->orderBy('sort_order')->first();
            if ($firstImage) {
                $this->setPrimaryImage($product, $firstImage->id);
            }
        } elseif ($primaryCount > 1) {
            // Multiple primaries - keep only the first one
            $images = $product->images()->where('is_primary', true)->orderBy('sort_order')->get();
            $keepPrimary = $images->first();

            $this->setPrimaryImage($product, $keepPrimary->id);
        }
    }

    /**
     * Get all images with their current state.
     *
     * @param Product $product
     * @return Collection
     */
    public function getProductImages(Product $product): Collection
    {
        return $product->images()->orderBy('sort_order')->get();
    }

    /**
     * Handle product image updates during product creation.
     *
     * First image is automatically set as primary.
     *
     * @param Product $product
     * @param array $images
     * @return void
     */
    public function handleInitialImages(Product $product, array $images): void
    {
        if (empty($images)) {
            return;
        }

        DB::transaction(function () use ($product, $images) {
            foreach ($images as $index => $image) {
                // Use compressor service to compress and store the image
                $path = $this->compressor->compressAndStore($image, 'products', 'public');

                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'is_primary' => ($index === 0), // First image is primary
                    'sort_order' => $index,
                ]);
            }
        });
    }

    /**
     * Handle product image updates during product update.
     *
     * @param Product $product
     * @param array $newImages
     * @param array $removeImages
     * @param int|null $primaryImageId
     * @param int|null $newImagePrimaryIndex
     * @return array IDs of newly uploaded images
     */
    public function handleImageUpdates(
        Product $product,
        array $newImages,
        array $removeImages,
        ?int $primaryImageId,
        ?int $newImagePrimaryIndex
    ): array {
        $uploadedNewImageIds = [];

        DB::transaction(function () use (
            $product,
            $newImages,
            $removeImages,
            $primaryImageId,
            $newImagePrimaryIndex,
            &$uploadedNewImageIds
        ) {
            // Step 1: Remove images marked for deletion
            if (!empty($removeImages)) {
                $this->removeImages($product, $removeImages);
            }

            // Step 2: Upload new images
            if (!empty($newImages)) {
                $uploadedImages = $this->uploadImages($product, $newImages);
                $uploadedNewImageIds = collect($uploadedImages)->pluck('id')->toArray();

                // If this is the selected primary new image, mark it
                if ($newImagePrimaryIndex !== null) {
                    $selectedImageId = $uploadedNewImageIds[$newImagePrimaryIndex] ?? null;
                    if ($selectedImageId) {
                        $primaryImageId = $selectedImageId;
                    }
                }
            }

            // Step 3: Set primary image atomically
            if ($primaryImageId) {
                $this->setPrimaryImage($product, $primaryImageId);
            } else {
                // Fallback: ensure at least one primary image exists
                $this->ensurePrimaryImageExists($product);
            }
        });

        return $uploadedNewImageIds;
    }
}
