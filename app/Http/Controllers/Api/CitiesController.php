<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CityResource;
use App\Models\City;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CitiesController extends Controller
{
    /**
     * Get all active cities with delivery fees.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $cities = City::active()
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => CityResource::collection($cities),
        ]);
    }

    /**
     * Get a specific city by ID.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $city = City::active()->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => CityResource::make($city),
        ]);
    }
}
