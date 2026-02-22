<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Invoice – Order {{ $order->order_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; line-height: 1.4; }
        .header { margin-bottom: 24px; padding-bottom: 16px; border-bottom: 2px solid #333; }
        .header h1 { margin: 0; font-size: 20px; }
        .meta { color: #666; font-size: 11px; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; margin: 16px 0; }
        th, td { padding: 8px 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f5f5f5; font-weight: 600; }
        .text-right { text-align: right; }
        .totals { margin-top: 24px; max-width: 280px; margin-left: auto; }
        .totals .row { padding: 4px 0; }
        .totals .row span:first-child { float: left; }
        .totals .row span:last-child { float: right; }
        .totals .grand { font-weight: bold; font-size: 14px; border-top: 2px solid #333; padding-top: 8px; margin-top: 8px; }
        .totals .row::after { content: ''; display: table; clear: both; }
        .two-cols { margin-top: 20px; }
        .two-cols .col { width: 48%; float: left; }
        .two-cols .col:last-child { float: right; }
        .two-cols::after { content: ''; display: table; clear: both; }
        .address-block { font-size: 11px; color: #555; }
        .footer { margin-top: 32px; padding-top: 16px; border-top: 1px solid #ddd; font-size: 10px; color: #888; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Invoice</h1>
        <p class="meta">{{ config('app.name') }} · Order {{ $order->order_number }}</p>
        <p class="meta">Date: {{ $order->created_at->format('F d, Y') }}</p>
    </div>

    <div class="two-cols">
        <div class="col">
            <strong>Bill to</strong>
            @if($order->billingAddress)
                <div class="address-block mt-1">
                    {{ $order->billingAddress->full_name }}<br>
                    {{ $order->billingAddress->address_line1 }}<br>
                    @if($order->billingAddress->address_line2){{ $order->billingAddress->address_line2 }}<br>@endif
                    {{ $order->billingAddress->city }}, {{ $order->billingAddress->state }} {{ $order->billingAddress->postal_code }}<br>
                    {{ $order->billingAddress->country }}<br>
                    {{ $order->billingAddress->phone }}
                </div>
            @else
                <div class="address-block">—</div>
            @endif
        </div>
        <div class="col">
            <strong>Ship to</strong>
            @if($order->shippingAddress)
                <div class="address-block mt-1">
                    {{ $order->shippingAddress->full_name }}<br>
                    {{ $order->shippingAddress->address_line1 }}<br>
                    @if($order->shippingAddress->address_line2){{ $order->shippingAddress->address_line2 }}<br>@endif
                    {{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }} {{ $order->shippingAddress->postal_code }}<br>
                    {{ $order->shippingAddress->country }}<br>
                    {{ $order->shippingAddress->phone }}
                </div>
            @else
                <div class="address-block">—</div>
            @endif
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>SKU</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Unit price</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ $item->product_name }}</td>
                <td>{{ $item->sku }}</td>
                <td class="text-right">{{ $item->quantity }}</td>
                <td class="text-right">${{ number_format($item->unit_price, 2) }}</td>
                <td class="text-right">${{ number_format($item->total_price, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div class="row"><span>Subtotal</span><span>${{ number_format($order->subtotal, 2) }}</span></div>
        @if($order->discount_total > 0)
            <div class="row"><span>Discount</span><span>-${{ number_format($order->discount_total, 2) }}</span></div>
        @endif
        <div class="row"><span>Shipping</span><span>${{ number_format($order->shipping_total, 2) }}</span></div>
        <div class="row"><span>Tax</span><span>${{ number_format($order->tax_total, 2) }}</span></div>
        <div class="row grand"><span>Total</span><span>${{ number_format($order->grand_total, 2) }}</span></div>
    </div>

    <div class="footer">
        Payment: {{ ucfirst(str_replace('_', ' ', $order->payment_method ?? 'N/A')) }} · Status: {{ ucfirst($order->status) }}
    </div>
</body>
</html>
