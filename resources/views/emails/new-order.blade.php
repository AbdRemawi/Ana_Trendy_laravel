<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Order {{ $order->order_number }}</title>
</head>
<body style="margin:0;padding:0;background:#f4f4f7;font-family:Arial,Helvetica,sans-serif;color:#333;">
    <div style="max-width:640px;margin:0 auto;padding:24px;">
        <div style="background:#111827;color:#ffffff;padding:20px 24px;border-radius:8px 8px 0 0;">
            <h1 style="margin:0;font-size:20px;">🛒 New Order Received</h1>
            <p style="margin:6px 0 0;font-size:13px;color:#d1d5db;">A new order needs your review.</p>
        </div>

        <div style="background:#ffffff;padding:24px;border:1px solid #e5e7eb;border-top:none;">
            <table style="width:100%;border-collapse:collapse;font-size:14px;">
                <tr>
                    <td style="padding:6px 0;color:#6b7280;">Order Number</td>
                    <td style="padding:6px 0;text-align:right;font-weight:bold;">{{ $order->order_number }}</td>
                </tr>
                <tr>
                    <td style="padding:6px 0;color:#6b7280;">Status</td>
                    <td style="padding:6px 0;text-align:right;">{{ $order->status_label }}</td>
                </tr>
                <tr>
                    <td style="padding:6px 0;color:#6b7280;">Customer</td>
                    <td style="padding:6px 0;text-align:right;">{{ $order->full_name }}</td>
                </tr>
                <tr>
                    <td style="padding:6px 0;color:#6b7280;">City</td>
                    <td style="padding:6px 0;text-align:right;">{{ $order->city?->name ?? '—' }}</td>
                </tr>
                <tr>
                    <td style="padding:6px 0;color:#6b7280;">Address</td>
                    <td style="padding:6px 0;text-align:right;">{{ $order->address }}</td>
                </tr>
                <tr>
                    <td style="padding:6px 0;color:#6b7280;">Phone</td>
                    <td style="padding:6px 0;text-align:right;">{{ $order->mobiles->pluck('phone_number')->implode(', ') ?: '—' }}</td>
                </tr>
                @if($order->notes)
                <tr>
                    <td style="padding:6px 0;color:#6b7280;">Notes</td>
                    <td style="padding:6px 0;text-align:right;">{{ $order->notes }}</td>
                </tr>
                @endif
                <tr>
                    <td style="padding:6px 0;color:#6b7280;">Placed At</td>
                    <td style="padding:6px 0;text-align:right;">{{ $order->created_at->format('Y-m-d H:i') }}</td>
                </tr>
            </table>

            <h2 style="font-size:16px;margin:24px 0 8px;">Items</h2>
            <table style="width:100%;border-collapse:collapse;font-size:14px;">
                <thead>
                    <tr style="background:#f9fafb;">
                        <th style="text-align:left;padding:8px;border-bottom:1px solid #e5e7eb;">Product</th>
                        <th style="text-align:center;padding:8px;border-bottom:1px solid #e5e7eb;">Qty</th>
                        <th style="text-align:right;padding:8px;border-bottom:1px solid #e5e7eb;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td style="padding:8px;border-bottom:1px solid #f3f4f6;">{{ $item->product?->name ?? 'Product #' . $item->product_id }}</td>
                        <td style="padding:8px;text-align:center;border-bottom:1px solid #f3f4f6;">{{ $item->quantity }}</td>
                        <td style="padding:8px;text-align:right;border-bottom:1px solid #f3f4f6;">{{ number_format($item->total_price, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <table style="width:100%;border-collapse:collapse;font-size:14px;margin-top:16px;">
                <tr>
                    <td style="padding:4px 0;color:#6b7280;">Subtotal</td>
                    <td style="padding:4px 0;text-align:right;">{{ number_format($order->subtotal_products, 2) }}</td>
                </tr>
                <tr>
                    <td style="padding:4px 0;color:#6b7280;">Delivery</td>
                    <td style="padding:4px 0;text-align:right;">{{ number_format($order->real_delivery_fee, 2) }}</td>
                </tr>
                @if($order->coupon_discount_amount > 0)
                <tr>
                    <td style="padding:4px 0;color:#6b7280;">Coupon Discount</td>
                    <td style="padding:4px 0;text-align:right;">- {{ number_format($order->coupon_discount_amount, 2) }}</td>
                </tr>
                @endif
                <tr>
                    <td style="padding:10px 0 0;font-weight:bold;font-size:16px;border-top:2px solid #111827;">Total</td>
                    <td style="padding:10px 0 0;text-align:right;font-weight:bold;font-size:16px;border-top:2px solid #111827;">{{ number_format($order->total_price_for_customer, 2) }}</td>
                </tr>
            </table>
        </div>

        <div style="background:#f9fafb;padding:16px 24px;border:1px solid #e5e7eb;border-top:none;border-radius:0 0 8px 8px;text-align:center;font-size:12px;color:#9ca3af;">
            This is an automated notification. Please log in to review and process this order.
        </div>
    </div>
</body>
</html>
