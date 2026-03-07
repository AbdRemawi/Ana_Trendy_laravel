<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CheckoutRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderMobile;
use App\Models\Product;
use App\Services\OrderCalculationService;
use App\Services\CouponValidationService;
use App\Services\InventoryService;
use App\Enums\OrderStatus;
use App\Enums\CouponType;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function __construct(
        private OrderCalculationService $calculationService,
        private CouponValidationService $couponValidationService,
        private InventoryService $inventoryService
    ) {}

    public function store(CheckoutRequest $request): JsonResponse
    {
        return DB::transaction(function () use ($request) {
            // Prepare cart items for stock validation
            $cartItems = collect($request->cartItems)->map(fn($item) => [
                'product_id' => $item['id'],
                'quantity' => $item['quantity']
            ])->toArray();

            // Validate and get coupon
            $coupon = null;
            $couponDiscountAmount = 0;
            $freeDeliveryDiscount = 0;

            if ($request->promoCode) {
                $validation = $this->couponValidationService->validate(
                    $request->promoCode,
                    $request->subtotal
                );
                if (!$validation['valid']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid coupon',
                        'error' => $validation['error']
                    ], 422);
                }
                $coupon = $validation['coupon'];
                $couponDiscountAmount = $this->calculationService->calculateCouponDiscount(
                    $request->subtotal,
                    $coupon
                );

                // Handle free delivery coupon
                if ($coupon->type === CouponType::FREE_DELIVERY->value) {
                    $freeDeliveryDiscount = $request->shipping;
                }
            }

            // Validate stock availability
            $stockErrors = $this->inventoryService->validateStockAvailability($cartItems);
            if (!empty($stockErrors)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock not available',
                    'errors' => $stockErrors
                ], 422);
            }

            // Get product details for order items
            $productIds = collect($request->cartItems)->pluck('id');
            $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

            // Generate order number
            $orderNumber = $this->generateOrderNumber();

            // Create order (using frontend prices)
            $order = Order::create([
                'order_number' => $orderNumber,
                'full_name' => $request->fullName,
                'city_id' => $request->cityId,
                'address' => $request->address,
                'delivery_courier_id' => null,
                'display_delivery_fee' => $request->shipping,
                'real_delivery_fee' => $request->shipping,
                'subtotal_products' => $request->subtotal,
                'coupon_id' => $coupon?->id,
                'coupon_discount_amount' => $couponDiscountAmount,
                'free_delivery_discount' => $freeDeliveryDiscount,
                'actual_charge' => $request->total,
                'total_price_for_customer' => $request->total,
                'status' => OrderStatus::PROCESSING->value,
                'notes' => $request->notes,
            ]);

            // Create order items
            foreach ($request->cartItems as $item) {
                $product = $products->get($item['id']);
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'base_price' => $product->sale_price,
                    'coupon_discount_per_unit' => 0,
                    'unit_sale_price' => $product->sale_price,
                    'unit_cost_price' => $product->cost_price ?? 0,
                    'total_price' => $product->sale_price * $item['quantity'],
                ]);
            }

            // Create mobile numbers
            foreach ($request->mobileNumbers as $phoneNumber) {
                OrderMobile::create([
                    'order_id' => $order->id,
                    'phone_number' => $phoneNumber,
                ]);
            }

            // Decrease inventory
            $this->inventoryService->decreaseStockForOrder($order);

            // Increment coupon usage
            if ($coupon) {
                $coupon->incrementUsedCount();
            }

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'data' => [
                    'order_number' => $order->order_number,
                    'status' => $order->status,
                    'total' => $order->total_price_for_customer,
                ]
            ], 201);
        });
    }

    private function generateOrderNumber(): string
    {
        $date = now()->format('Ymd');
        $latestOrder = Order::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $latestOrder
            ? (int) substr($latestOrder->order_number, -4) + 1
            : 1;

        return "ORD-{$date}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}
