<!DOCTYPE html>
<html lang="{{ $locale ?? app()->getLocale() }}" dir="{{ $direction ?? 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('admin.order_details') }} - {{ $order->order_number }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <style>
        @page {
            size: A5;
            margin: 0;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        :root {
            --beige-bg: #f3e9d7;
            --beige-soft: #ece0c6;
            --cream: #faf3e6;
            --ink: #2b2419;
            --ink-soft: #6b5d48;
            --gold: #b08d57;
            --gold-deep: #8a6b3a;
            --line: #d9c8a8;
        }

        html, body {
            margin: 0;
            padding: 0;
            background: #d8c8a8;
            font-family: 'Tajawal', sans-serif;
            font-size: 10px;
            color: var(--ink);
            font-weight: 400;
        }

        .sheet {
            width: 148mm;
            min-height: 210mm;
            margin: 12px auto;
            background:
                linear-gradient(135deg, rgba(255,255,255,0.35) 0%, rgba(255,255,255,0) 60%),
                var(--beige-bg);
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.18);
        }

        /* Decorative inner gold border */
        .sheet::before {
            content: "";
            position: absolute;
            inset: 5mm;
            border: 1px solid var(--gold);
            pointer-events: none;
            z-index: 1;
        }
        .sheet::after {
            content: "";
            position: absolute;
            inset: 6.2mm;
            border: 0.4px solid rgba(176, 141, 87, 0.55);
            pointer-events: none;
            z-index: 1;
        }

        .inner {
            position: relative;
            z-index: 2;
            padding: 9mm 10mm;
        }

        /* ===== Header ===== */
        .header {
            text-align: center;
            padding-bottom: 6mm;
            position: relative;
        }
        .header img.logo {
            display: block;
            margin: 0 auto 3mm auto;
            max-height: 22mm;
            max-width: 60%;
            object-fit: contain;
        }
        .brand-name {
            font-size: 20px;
            letter-spacing: 6px;
            font-weight: 900;
            color: var(--ink);
            text-transform: uppercase;
            margin: 0;
        }
        .brand-tag {
            font-size: 9px;
            letter-spacing: 5px;
            color: var(--gold-deep);
            text-transform: uppercase;
            margin-top: 1mm;
            font-weight: 500;
        }

        .ornament {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin: 4mm 0 3mm 0;
        }
        .ornament .line {
            flex: 1;
            height: 1px;
            background: linear-gradient(to right, transparent, var(--gold) 50%, transparent);
            max-width: 28mm;
        }
        .ornament .diamond {
            width: 6px;
            height: 6px;
            background: var(--gold);
            transform: rotate(45deg);
        }

        .receipt-title {
            font-size: 11px;
            letter-spacing: 5px;
            text-transform: uppercase;
            color: var(--ink-soft);
            font-weight: 700;
        }

        .order-meta {
            display: flex;
            justify-content: space-between;
            font-size: 9px;
            color: var(--ink-soft);
            margin-top: 3mm;
            padding: 0 2mm;
        }
        .order-meta .order-no {
            font-size: 11px;
            font-weight: 700;
            color: var(--ink);
            letter-spacing: 1.5px;
        }

        /* ===== Customer ===== */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4mm;
            margin: 5mm 0 4mm 0;
        }
        .info-block .info-label {
            font-size: 8px;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            color: var(--gold-deep);
            margin-bottom: 1mm;
            font-weight: 700;
        }
        .info-block .info-value {
            font-size: 10.5px;
            color: var(--ink);
            font-weight: 500;
            line-height: 1.4;
        }

        /* ===== Items table ===== */
        .section-title {
            text-align: center;
            font-size: 10px;
            letter-spacing: 4px;
            text-transform: uppercase;
            color: var(--ink-soft);
            margin: 3mm 0 2mm 0;
            font-weight: 700;
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
            background: var(--cream);
            border: 1px solid var(--line);
        }
        table.items thead th {
            font-size: 8.5px;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--gold-deep);
            background: var(--beige-soft);
            border-bottom: 1px solid var(--gold);
            padding: 2.5mm 2mm;
            text-align: {{ ($direction ?? 'ltr') === 'rtl' ? 'right' : 'left' }};
            font-weight: 700;
        }
        table.items thead th.num {
            text-align: {{ ($direction ?? 'ltr') === 'rtl' ? 'left' : 'right' }};
        }
        table.items tbody td {
            padding: 2mm;
            font-size: 9.5px;
            border-bottom: 1px dashed var(--line);
            vertical-align: middle;
            text-align: {{ ($direction ?? 'ltr') === 'rtl' ? 'right' : 'left' }};
        }
        table.items tbody td.num {
            text-align: {{ ($direction ?? 'ltr') === 'rtl' ? 'left' : 'right' }};
            white-space: nowrap;
            font-variant-numeric: tabular-nums;
        }
        table.items tbody tr:last-child td { border-bottom: 0; }

        .product-cell {
            display: flex;
            align-items: center;
            gap: 2.5mm;
        }
        .product-cell .thumb {
            width: 11mm;
            height: 11mm;
            object-fit: cover;
            border: 1px solid var(--line);
            background: #fff;
            flex-shrink: 0;
        }
        .product-cell .thumb-placeholder {
            width: 11mm;
            height: 11mm;
            border: 1px dashed var(--line);
            background: #fff;
            flex-shrink: 0;
        }
        .product-cell .name {
            font-weight: 600;
            color: var(--ink);
            line-height: 1.3;
        }

        /* ===== Totals ===== */
        .totals-wrap {
            display: flex;
            justify-content: {{ ($direction ?? 'ltr') === 'rtl' ? 'flex-start' : 'flex-end' }};
            margin-top: 4mm;
        }
        table.totals {
            min-width: 65%;
            border-collapse: collapse;
        }
        table.totals td {
            padding: 1.5mm 2mm;
            font-size: 10px;
        }
        table.totals td.label {
            color: var(--ink-soft);
            text-align: {{ ($direction ?? 'ltr') === 'rtl' ? 'right' : 'left' }};
            letter-spacing: 1.5px;
            text-transform: uppercase;
            font-size: 9px;
            font-weight: 500;
        }
        table.totals td.value {
            color: var(--ink);
            font-weight: 600;
            text-align: {{ ($direction ?? 'ltr') === 'rtl' ? 'left' : 'right' }};
            white-space: nowrap;
            font-variant-numeric: tabular-nums;
        }
        table.totals tr.discount td.value { color: #a13030; }
        table.totals tr.grand td {
            border-top: 1.5px solid var(--gold);
            padding-top: 2.5mm;
            margin-top: 2mm;
        }
        table.totals tr.grand td.label {
            color: var(--ink);
            font-size: 10.5px;
            letter-spacing: 3px;
            font-weight: 700;
        }
        table.totals tr.grand td.value {
            font-size: 15px;
            font-weight: 900;
            color: var(--gold-deep);
            letter-spacing: 0.5px;
        }

        /* ===== Notes & footer ===== */
        .notes {
            margin-top: 4mm;
            padding: 3mm 4mm;
            border: 1px solid var(--line);
            background: var(--cream);
            font-size: 9.5px;
            line-height: 1.4;
            color: var(--ink);
        }
        .notes strong {
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--gold-deep);
            font-size: 9px;
            font-weight: 700;
        }

        .footer {
            margin-top: 6mm;
            text-align: center;
            color: var(--ink-soft);
        }
        .footer .thanks {
            font-size: 13px;
            letter-spacing: 6px;
            text-transform: uppercase;
            color: var(--ink);
            font-weight: 900;
        }
        .footer .sub {
            font-size: 8.5px;
            letter-spacing: 2px;
            margin-top: 1.5mm;
            color: var(--gold-deep);
            font-weight: 500;
        }

        /* ===== Toolbar (screen only) ===== */
        .toolbar {
            position: fixed;
            top: 14px;
            {{ ($direction ?? 'ltr') === 'rtl' ? 'left' : 'right' }}: 14px;
            z-index: 1000;
            display: flex;
            gap: 8px;
        }
        .toolbar button,
        .toolbar a {
            padding: 9px 16px;
            font-size: 12px;
            font-weight: 600;
            border: 0;
            border-radius: 6px;
            cursor: pointer;
            background: var(--ink);
            color: #fff;
            text-decoration: none;
            font-family: inherit;
        }
        .toolbar a.secondary {
            background: #fff;
            color: var(--ink);
            border: 1px solid var(--ink);
        }

        @media print {
            body { background: var(--beige-bg); }
            .toolbar { display: none !important; }
            .sheet {
                margin: 0;
                width: 148mm;
                min-height: 210mm;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button type="button" onclick="window.print()">{{ __('admin.print_pdf') }}</button>
        <a class="secondary" href="{{ route('admin.orders.show', $order) }}">{{ __('admin.back') }}</a>
    </div>

    <div class="sheet">
        <div class="inner">
            {{-- Logo + brand --}}
            <div class="header">
                <img class="logo" src="{{ asset('images/logo.png') }}" alt="Logo">
                <div class="ornament">
                    <span class="line"></span>
                    <span class="diamond"></span>
                    <span class="receipt-title">{{ __('admin.order_details') }}</span>
                    <span class="diamond"></span>
                    <span class="line"></span>
                </div>
                <div class="order-meta">
                    <span class="order-no">{{ $order->order_number }}</span>
                    <span>{{ $order->created_at->format('Y-m-d H:i') }}</span>
                </div>
            </div>

            {{-- Customer info --}}
            <div class="info-grid">
                <div class="info-block">
                    <div class="info-label">{{ __('admin.customer_name') }}</div>
                    <div class="info-value">{{ $order->full_name }}</div>
                </div>
                <div class="info-block" style="text-align: {{ ($direction ?? 'ltr') === 'rtl' ? 'left' : 'right' }};">
                    <div class="info-label">{{ __('admin.order_phone_numbers') }}</div>
                    <div class="info-value">
                        @foreach($order->mobiles as $mobile){{ $mobile->phone_number }}{{ !$loop->last ? ' / ' : '' }}@endforeach
                    </div>
                </div>
                <div class="info-block">
                    <div class="info-label">{{ __('admin.order_city') }}</div>
                    <div class="info-value">{{ $order->city->name ?? '-' }}</div>
                </div>
                <div class="info-block" style="text-align: {{ ($direction ?? 'ltr') === 'rtl' ? 'left' : 'right' }};">
                    <div class="info-label">{{ __('admin.order_status') }}</div>
                    <div class="info-value">{{ __('admin.status_' . $order->status) }}</div>
                </div>
                <div class="info-block" style="grid-column: 1 / -1;">
                    <div class="info-label">{{ __('admin.customer_address') }}</div>
                    <div class="info-value">{{ $order->address }}</div>
                </div>
            </div>

            {{-- Items --}}
            <div class="section-title">{{ __('admin.order_items') }}</div>
            <table class="items">
                <thead>
                    <tr>
                        <th>{{ __('admin.item_product') }}</th>
                        <th class="num">{{ __('admin.item_quantity') }}</th>
                        <th class="num">{{ __('admin.item_base_price') }}</th>
                        <th class="num">{{ __('admin.item_total') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        @php
                            $img = optional(optional($item->product->primaryImage)->first())->image_url;
                        @endphp
                        <tr>
                            <td>
                                <div class="product-cell">
                                    @if($img)
                                        <img class="thumb" src="{{ $img }}" alt="">
                                    @else
                                        <div class="thumb-placeholder"></div>
                                    @endif
                                    <div class="name">{{ $item->product->name ?? '-' }}</div>
                                </div>
                            </td>
                            <td class="num">{{ $item->quantity }}</td>
                            <td class="num">{{ number_format($item->base_price, 2) }}</td>
                            <td class="num">{{ number_format($item->total_price, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Totals --}}
            <div class="totals-wrap">
                <table class="totals">
                    <tr>
                        <td class="label">{{ __('admin.subtotal_products') }}</td>
                        <td class="value">{{ number_format($order->subtotal_products, 2) }} JOD</td>
                    </tr>
                    @if($order->coupon_discount_amount > 0)
                        <tr class="discount">
                            <td class="label">{{ __('admin.coupon_discount') }}</td>
                            <td class="value">-{{ number_format($order->coupon_discount_amount, 2) }} JOD</td>
                        </tr>
                    @endif
                    @if($order->real_delivery_fee)
                        <tr>
                            <td class="label">{{ __('admin.real_delivery_fees') }}</td>
                            <td class="value">{{ number_format($order->real_delivery_fee, 3) }} JOD</td>
                        </tr>
                    @endif
                    <tr class="grand">
                        <td class="label">{{ __('admin.total_price') }}</td>
                        <td class="value">{{ number_format($order->total_price_for_customer, 2) }} JOD</td>
                    </tr>
                </table>
            </div>

            @if($order->notes)
                <div class="notes">
                    <strong>{{ __('admin.order_notes') }}</strong><br>
                    {{ $order->notes }}
                </div>
            @endif

            <div class="footer">
                <div class="thanks">{{ config('app.name') }}</div>
                <div class="ornament" style="margin: 1.5mm 0;">
                    <span class="line"></span>
                    <span class="diamond"></span>
                    <span class="line"></span>
                </div>
                <div class="sub">{{ now()->format('Y-m-d H:i') }}</div>
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('load', function () {
            setTimeout(function () { window.print(); }, 350);
        });
    </script>
</body>
</html>
