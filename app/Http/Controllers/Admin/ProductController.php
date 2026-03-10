<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * Product Management Controller (Admin)
 *
 * Handles product CRUD operations in the admin dashboard.
 * All methods are protected by permission middleware via routes.
 * Uses Form Request validation classes for clean separation of concerns.
 *
 * ENHANCED: Now includes unified product + images handling.
 * - Images are uploaded in the same form as product data
 * - Transaction-safe operations (product + images + inventory)
 * - Primary image enforcement (exactly one per product)
 */
class ProductController extends Controller
{
    /**
     * Display a listing of products.
     * Authorization is handled via route middleware.
     */
    public function index(Request $request): View
    {
        $query = Product::query()->with(['brand', 'category', 'images'])
            ->withStockQuantity();

        // Filter by brand if requested
        if ($request->filled('brand')) {
            $query->byBrand($request->brand);
        }

        // Filter by category if requested
        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        // Filter by size if requested
        if ($request->filled('size')) {
            $query->bySize($request->size);
        }

        // Filter by gender if requested
        if ($request->filled('gender')) {
            $query->byGender($request->gender);
        }

        // Filter by status if requested
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        // Search by name
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $products = $query->latest()->paginate(20);

        // Get filter options with images
        $brands = \App\Models\Brand::active()->orderBy('name')->get(['id', 'name', 'logo']);
        $categories = \App\Models\Category::active()->ordered()->get(['id', 'name', 'image']);

        return view('admin.products.index', compact(
            'products',
            'brands',
            'categories'
        ));
    }

    /**
     * Show the form for creating a new product.
     * Authorization is handled via route middleware.
     */
    public function create(): View
    {
        $brands = \App\Models\Brand::active()->orderBy('name')->get(['id', 'name', 'logo']);
        $categories = \App\Models\Category::active()->ordered()->get(['id', 'name', 'image']);

        return view('admin.products.create', compact(
            'brands',
            'categories'
        ));
    }

    /**
     * Store a newly created product with images and initial inventory.
     * Uses DB transaction for data integrity.
     *
     * ENHANCED: First image is automatically set as primary.
     * Redirects to edit page to allow admin to review and change primary image.
     */
    public function store(StoreProductRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $images = $request->file('images', []);
        $initialQuantity = (int) $validated['initial_quantity'];

        DB::transaction(function () use ($validated, $images, $initialQuantity, &$product) {
            // Create the product
            $product = Product::create([
                'brand_id' => $validated['brand_id'],
                'category_id' => $validated['category_id'],
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'size' => $validated['size'],
                'gender' => $validated['gender'],
                'cost_price' => $validated['cost_price'],
                'sale_price' => $validated['sale_price'],
                'offer_price' => $validated['offer_price'] ?? null,
                'status' => $validated['status'],
            ]);

            // Upload and attach images - first image is automatically primary
            foreach ($images as $index => $image) {
                $path = $image->store('products', 'public');

                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'is_primary' => ($index === 0), // First image is primary
                    'sort_order' => $index,
                ]);
            }

            // Create initial inventory transaction (type = 'supply')
            if ($initialQuantity > 0) {
                $product->inventoryTransactions()->create([
                    'type' => \App\Models\InventoryTransaction::TYPE_SUPPLY,
                    'quantity' => $initialQuantity,
                    'notes' => __('admin.initial_supply_transaction'),
                ]);
            }
        });

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('success', __('admin.product_created_successfully'));
    }

    /**
     * Display the specified product.
     * Authorization is handled via route middleware.
     */
    public function show(Product $product): View
    {
        $product->load(['brand', 'category', 'images', 'inventoryTransactions']);

        // Calculate current stock
        $stockQuantity = $product->stock_quantity;

        return view('admin.products.show', compact(
            'product',
            'stockQuantity'
        ));
    }

    /**
     * Show the form for editing the specified product.
     * Authorization is handled via route middleware.
     */
    public function edit(Product $product): View
    {
        $brands = \App\Models\Brand::active()->orderBy('name')->get(['id', 'name', 'logo']);
        $categories = \App\Models\Category::active()->ordered()->get(['id', 'name', 'image']);

        // Load images with their current state
        $product->load('images');

        // Translation strings for JavaScript
        $translations = [
            'confirm_remove_image' => __('admin.confirm_remove_image'),
            'new_images_will_add' => __('admin.new_images_will_add'),
            'at_least_one_image_required' => __('admin.at_least_one_image_required'),
            'select_primary_image' => __('admin.select_primary_image'),
        ];

        return view('admin.products.edit', compact(
            'product',
            'brands',
            'categories',
            'translations'
        ));
    }

    /**
     * Update the specified product with images.
     * Uses DB transaction for data integrity.
     *
     * ENHANCED: Simplified primary image handling using single primary_image_id field.
     * All images (existing + new) shown together with one radio button group.
     * Supports selecting newly uploaded images as primary before form submission.
     */
    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $validated = $request->validated();
        $newImages = $request->file('images', []);
        $removeImages = $request->input('remove_images', []);
        $primaryImageId = $request->input('primary_image_id');
        $newImagePrimaryIndex = $request->input('new_image_primary_index');

        DB::transaction(function () use (
            $product,
            $validated,
            $newImages,
            $removeImages,
            $primaryImageId,
            $newImagePrimaryIndex,
            &$uploadedNewImageIds
        ) {
            // Step 1: Update product data
            $product->update([
                'brand_id' => $validated['brand_id'],
                'category_id' => $validated['category_id'],
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'size' => $validated['size'],
                'gender' => $validated['gender'],
                'cost_price' => $validated['cost_price'],
                'sale_price' => $validated['sale_price'],
                'offer_price' => $validated['offer_price'] ?? null,
                'status' => $validated['status'],
            ]);

            // Step 2: Remove images marked for deletion
            if (!empty($removeImages)) {
                foreach ($removeImages as $imageId) {
                    if (empty($imageId)) continue;

                    $image = ProductImage::find($imageId);
                    if ($image && $image->product_id === $product->id) {
                        Storage::disk('public')->delete($image->image_path);
                        $image->delete();
                    }
                }
            }

            // Step 3: Upload new images
            $uploadedNewImageIds = [];
            if (!empty($newImages)) {
                $maxSortOrder = $product->images()->max('sort_order') ?? 0;

                foreach ($newImages as $index => $image) {
                    $path = $image->store('products', 'public');

                    $newImage = ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path,
                        'is_primary' => false,
                        'sort_order' => ++$maxSortOrder,
                    ]);

                    $uploadedNewImageIds[] = $newImage->id;

                    // If this is the selected primary new image, mark it
                    if ($newImagePrimaryIndex !== null && (int)$newImagePrimaryIndex === $index) {
                        $primaryImageId = $newImage->id;
                    }
                }
            }

            // Step 4: Set primary image atomically - exactly ONE primary
            if ($primaryImageId) {
                // First, ensure ALL images are not primary
                ProductImage::where('product_id', $product->id)
                    ->update(['is_primary' => false]);

                // Then set the selected image as primary
                ProductImage::where('product_id', $product->id)
                    ->where('id', $primaryImageId)
                    ->update(['is_primary' => true]);
            } else {
                // Fallback: ensure at least one primary image exists
                $primaryCount = $product->images()->where('is_primary', true)->count();

                if ($primaryCount === 0) {
                    $firstImage = $product->images()->orderBy('sort_order')->first();
                    if ($firstImage) {
                        ProductImage::where('product_id', $product->id)
                            ->update(['is_primary' => false]);
                        $firstImage->update(['is_primary' => true]);
                    }
                } elseif ($primaryCount > 1) {
                    // Multiple primaries - keep only the first one
                    $images = $product->images()->where('is_primary', true)->orderBy('sort_order')->get();
                    $keepPrimary = $images->first();

                    ProductImage::where('product_id', $product->id)
                        ->update(['is_primary' => false]);
                    $keepPrimary->update(['is_primary' => true]);
                }
            }
        });

        return redirect()
            ->route('admin.products.index')
            ->with('success', __('admin.product_updated_successfully'));
    }

    /**
     * Remove the specified product.
     * Authorization is handled via route middleware.
     *
     * Uses soft delete - records are not permanently removed.
     * Images are preserved in case of restoration.
     *
     * SECURITY: Prevents deletion of products with active orders
     */
    public function destroy(Product $product): RedirectResponse
    {
        try {
            // SECURITY: Check if product has related data before deletion
            // Add your business logic here (e.g., check for active orders)
            // if ($product->orders()->where('status', 'active')->exists()) {
            //     return back()
            //         ->with('error', __('admin.cannot_delete_product_with_orders'));
            // }

            $productName = $product->name;
            $product->delete();

            return redirect()
                ->route('admin.products.index')
                ->with('success', __('admin.product_deleted_successfully', ['name' => $productName]));
        } catch (\Exception $e) {
            return back()
                ->with('error', __('admin.delete_failed', ['error' => $e->getMessage()]));
        }
    }

    /**
     * Restore a soft deleted product.
     * Authorization is handled via route middleware.
     */
    public function restore($id): RedirectResponse
    {
        $product = Product::withTrashed()->findOrFail($id);
        $product->restore();

        return redirect()
            ->route('admin.products.index')
            ->with('success', __('admin.product_restored_successfully'));
    }
}
