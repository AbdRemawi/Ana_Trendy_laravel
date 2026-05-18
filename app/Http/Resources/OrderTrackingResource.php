<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Order
 */
class OrderTrackingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'order_number' => $this->order_number,
            'status' => $this->status,
            'status_label' => $this->status_label,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),

            'customer' => [
                'full_name' => $this->full_name,
                'city' => $this->whenLoaded('city', fn() => $this->city?->name),
                'address' => $this->address,
            ],

            'delivery' => [
                'courier_name' => $this->whenLoaded(
                    'deliveryCourier',
                    fn() => $this->deliveryCourier?->name
                ),
            ],

            'items' => OrderTrackingItemResource::collection($this->whenLoaded('items')),

            'totals' => [
                'subtotal_products' => (float) $this->subtotal_products,
                'delivery_fee' => (float) $this->real_delivery_fee,
                'coupon_discount' => (float) $this->coupon_discount_amount,
                'free_delivery_discount' => (float) $this->free_delivery_discount,
                'total' => (float) $this->total_price_for_customer,
            ],
        ];
    }
}
