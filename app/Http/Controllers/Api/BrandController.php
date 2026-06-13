<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BrandResource;
use App\Http\Resources\BrandWithProductsResource;
use App\Models\Brand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    /**
     * Display a listing of active brands.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $brands = Brand::query()
            ->active()
            ->orderBy('name')
            ->get();

        return BrandResource::collection($brands)->response();
    }

    /**
     * Display the specified brand with its products.
     *
     * @param string $slug The brand slug
     * @param Request $request
     * @return JsonResponse
     */
    public function show(string $slug, Request $request): JsonResponse
    {
        $brand = Brand::query()
            ->where('slug', $slug)
            ->active()
            ->with([
                'products' => function ($query) {
                    $query->active()
                        ->inStock()
                        ->select([
                            'id', 'name', 'slug', 'description',
                            'brand_id', 'category_id',
                            'sale_price', 'offer_price',
                            'size', 'gender',
                            'status', 'created_at',
                        ])
                        ->with([
                            'brand:id,name,slug',
                            'primaryImage:id,product_id,image_path,is_primary',
                        ])
                        ->withStockQuantity()
                        ->orderBy('name');
                }
            ])
            ->firstOrFail();

        return (new BrandWithProductsResource($brand))
            ->response()
            ->setStatusCode(200);
    }
}
