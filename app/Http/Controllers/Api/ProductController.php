<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductDetailResource;
use App\Http\Requests\Api\ProductIndexRequest;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of active products with filtering and sorting.
     * Includes metadata for available brands, price range, sizes, and genders.
     *
     * @param ProductIndexRequest $request
     * @return JsonResponse
     */
    public function index(ProductIndexRequest $request): JsonResponse
    {
        $query = Product::query()
            ->active()
            ->with(['brand', 'images'])
            ->withStockQuantity();

        // Apply filters
        $this->applyFilters($query, $request);

        // Get filter metadata BEFORE pagination (based on filtered results)
        $metadata = $this->getFilterMetadata($query);

        // Apply sorting
        $this->applySorting($query, $request);

        // Paginate
        $products = $query->paginate($request->perPage());

        $response = ProductResource::collection($products)->response();
        $data = json_decode($response->getContent(), true);

        // Add metadata to response
        $data['meta'] = array_merge($data['meta'] ?? [], $metadata);

        return response()->json($data);
    }

    /**
     * Display a single product by ID.
     * Returns 404 if product is not found or inactive.
     *
     * @param Request $request
     * @param int $id Product ID
     * @return ProductDetailResource
     */
    public function show(Request $request, int $id): ProductDetailResource
    {
        $product = Product::query()
            ->active()
            ->with(['brand', 'category', 'images'])
            ->withStockQuantity()
            ->findOrFail($id);

        return new ProductDetailResource($product);
    }

    /**
     * Display a single product by ID or slug.
     * Supports both numeric ID and string slug.
     * Returns 404 if product is not found or inactive.
     *
     * @param Request $request
     * @param string $id_or_slug Product ID or slug
     * @return ProductDetailResource
     */
    public function showByIdOrSlug(Request $request, string $id_or_slug): ProductDetailResource
    {
        // Check if it's a numeric ID
        if (ctype_digit($id_or_slug)) {
            $product = Product::query()
                ->active()
                ->with(['brand', 'category', 'images'])
                ->withStockQuantity()
                ->where('id', $id_or_slug)
                ->firstOrFail();
        } else {
            // It's a slug
            $product = Product::query()
                ->active()
                ->with(['brand', 'category', 'images'])
                ->withStockQuantity()
                ->where('slug', $id_or_slug)
                ->firstOrFail();
        }

        return new ProductDetailResource($product);
    }

    /**
     * Apply filters to the product query.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param ProductIndexRequest $request
     * @return void
     */
    private function applyFilters(\Illuminate\Database\Eloquent\Builder $query, ProductIndexRequest $request): void
    {
        // Brand filter (by slug or ID)
        $this->applyBrandFilter($query, $request);

        // Category filter (by slug or ID)
        $this->applyCategoryFilter($query, $request);

        // Price range filter
        $this->applyPriceFilter($query, $request);

        // Attribute filters (size, gender)
        $this->applyAttributeFilters($query, $request);

        // Offers only filter
        if ($request->boolean('offers_only')) {
            $query->withOffers();
        }
    }

    /**
     * Apply brand filter to query.
     * Supports single or multiple brands by ID or slug.
     * Returns empty collection if brand(s) not found (no 404).
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param ProductIndexRequest $request
     * @return void
     */
    private function applyBrandFilter(\Illuminate\Database\Eloquent\Builder $query, ProductIndexRequest $request): void
    {
        // Multiple brand IDs
        if ($request->filled('brand_ids')) {
            $query->whereIn('brand_id', $request->input('brand_ids'));
            return;
        }

        // Single brand ID
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->integer('brand_id'));
            return;
        }

        // Multiple brand slugs
        if ($request->filled('brands')) {
            $brandIds = Brand::whereIn('slug', $request->input('brands'))
                ->pluck('id')
                ->toArray();

            if (!empty($brandIds)) {
                $query->whereIn('brand_id', $brandIds);
            } else {
                // No brands found - return empty result
                $query->where('brand_id', 0);
            }
            return;
        }

        // Single brand slug
        if ($request->filled('brand')) {
            $brand = Brand::where('slug', $request->string('brand'))->first();
            if ($brand) {
                $query->where('brand_id', $brand->id);
            } else {
                // Brand not found - return empty result
                $query->where('brand_id', 0);
            }
        }
    }

    /**
     * Apply category filter to query.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param ProductIndexRequest $request
     * @return void
     */
    private function applyCategoryFilter(\Illuminate\Database\Eloquent\Builder $query, ProductIndexRequest $request): void
    {
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->integer('category_id'));
            return;
        }

        if ($request->filled('category')) {
            $category = Category::where('slug', $request->string('category'))->first();
            if ($category) {
                $query->where('category_id', $category->id);
            } else {
                $query->where('category_id', 0);
            }
        }
    }

    /**
     * Apply price range filter.
     * Filters on effective price (offer_price if exists, else sale_price).
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param ProductIndexRequest $request
     * @return void
     */
    private function applyPriceFilter(\Illuminate\Database\Eloquent\Builder $query, ProductIndexRequest $request): void
    {
        if ($request->filled('min_price')) {
            $minPrice = (float) $request->input('min_price');

            // Filter: COALESCE(offer_price, sale_price) >= min_price
            $query->whereRaw('COALESCE(offer_price, sale_price) >= ?', [$minPrice]);
        }

        if ($request->filled('max_price')) {
            $maxPrice = (float) $request->input('max_price');

            // Filter: COALESCE(offer_price, sale_price) <= max_price
            $query->whereRaw('COALESCE(offer_price, sale_price) <= ?', [$maxPrice]);
        }
    }

    /**
     * Apply attribute filters (size, gender) to query.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param ProductIndexRequest $request
     * @return void
     */
    private function applyAttributeFilters(\Illuminate\Database\Eloquent\Builder $query, ProductIndexRequest $request): void
    {
        if ($request->filled('size')) {
            $query->bySize($request->string('size'));
        }

        if ($request->filled('gender')) {
            $query->byGender($request->string('gender'));
        }
    }

    /**
     * Apply sorting to query.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param ProductIndexRequest $request
     * @return void
     */
    private function applySorting(\Illuminate\Database\Eloquent\Builder $query, ProductIndexRequest $request): void
    {
        $sort = $request->input('sort', 'newest');

        match ($sort) {
            'price_low_high' => $query->orderByRaw('COALESCE(offer_price, sale_price) ASC'),
            'price_high_low' => $query->orderByRaw('COALESCE(offer_price, sale_price) DESC'),
            'newest' => $query->orderBy('created_at', 'desc'),
            default => $query->orderBy('created_at', 'desc'),
        };
    }

    /**
     * Get filter metadata based on the current filtered query.
     * Returns available brands, price range, sizes, and genders.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return array
     */
    private function getFilterMetadata(\Illuminate\Database\Eloquent\Builder $query): array
    {
        // Clone the query to avoid modifying the original
        $metadataQuery = clone $query;

        // Get products for metadata calculation (only needed fields)
        $products = $metadataQuery
            ->select('id', 'brand_id', 'sale_price', 'offer_price', 'size', 'gender')
            ->withoutEagerLoads()
            ->get();

        // Calculate price range based on effective prices
        $effectivePrices = $products->map(function ($product) {
            return $product->offer_price && $product->offer_price < $product->sale_price
                ? (float) $product->offer_price
                : (float) $product->sale_price;
        });

        $minPrice = $effectivePrices->min() ?? 0;
        $maxPrice = $effectivePrices->max() ?? 0;

        // Get unique brands from the filtered products
        $brandIds = $products->pluck('brand_id')->unique()->filter()->values();
        $brands = Brand::whereIn('id', $brandIds)
            ->select('id', 'name', 'slug')
            ->orderBy('name')
            ->get();

        // Get unique sizes (sorted in natural order: S, M, L, XL, XXL)
        $sizeOrder = ['S' => 1, 'M' => 2, 'L' => 3, 'XL' => 4, 'XXL' => 5];
        $sizes = $products->pluck('size')
            ->unique()
            ->filter()
            ->sort(fn($a, $b) => ($sizeOrder[$a] ?? 99) <=> ($sizeOrder[$b] ?? 99))
            ->values();

        // Get unique genders (sorted alphabetically)
        $genders = $products->pluck('gender')
            ->unique()
            ->filter()
            ->sort()
            ->values();

        // Count products with offers
        $offerCount = $products->filter(function ($product) {
            return $product->offer_price && $product->offer_price < $product->sale_price;
        })->count();

        return [
            'filters' => [
                'brands' => $brands->map(function ($brand) {
                    return [
                        'id' => $brand->id,
                        'name' => $brand->name,
                        'slug' => $brand->slug,
                    ];
                })->values(),
                'price_range' => [
                    'min' => $minPrice,
                    'max' => $maxPrice,
                ],
                'sizes' => $sizes,
                'genders' => $genders,
                'has_offers' => $offerCount > 0,
                'offer_count' => $offerCount,
                'total_products' => $products->count(),
            ],
        ];
    }
}
