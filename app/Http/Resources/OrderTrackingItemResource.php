<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\OrderItem
 */
class OrderTrackingItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'product_name' => $this->whenLoaded('product', fn() => $this->product?->name),
            'quantity' => (int) $this->quantity,
            'unit_sale_price' => (float) $this->unit_sale_price,
            'total_price' => (float) $this->total_price,
        ];
    }
}
