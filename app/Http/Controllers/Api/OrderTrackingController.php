<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderTrackingResource;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderTrackingController extends Controller
{
    /**
     * Look up all orders associated with a customer phone number.
     */
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'phone' => ['required', 'string', 'min:5', 'max:20'],
        ]);

        $raw = trim($validated['phone']);
        $normalized = $this->normalizePhone($raw);

        $orders = Order::whereHas('mobiles', function ($q) use ($normalized, $raw) {
                $q->where('phone_number', $normalized)
                  ->orWhere('phone_number', $raw);
            })
            ->with(['city', 'deliveryCourier', 'items.product'])
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        return response()->json([
            'success' => true,
            'data' => OrderTrackingResource::collection($orders),
        ], 200);
    }

    /**
     * Normalize a phone number: drop non-digits, then convert a leading
     * country code (962) into the local `0` prefix so common Jordanian
     * formats compare equal.
     */
    private function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if (str_starts_with($digits, '00962')) {
            $digits = '0' . substr($digits, 5);
        } elseif (str_starts_with($digits, '962')) {
            $digits = '0' . substr($digits, 3);
        }

        return $digits;
    }
}
