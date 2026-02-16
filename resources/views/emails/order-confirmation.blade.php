<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <style>
        body { font-family: sans-serif; line-height: 1.6; color: #374151; margin: 0; padding: 0; background: #f3f4f6; }
        .wrapper { max-width: 600px; margin: 0 auto; padding: 24px; }
        .card { background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden; }
        .header { background: #4f46e5; color: #fff; padding: 20px 24px; }
        .header h1 { margin: 0; font-size: 20px; font-weight: 600; }
        .content { padding: 24px; }
        .order-number { font-size: 18px; font-weight: 600; color: #1f2937; margin-bottom: 16px; }
        .thanks { margin-bottom: 20px; color: #4b5563; }
        table.items { width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 14px; }
        table.items th { text-align: left; padding: 10px 0; border-bottom: 2px solid #e5e7eb; color: #6b7280; font-weight: 600; }
        table.items td { padding: 10px 0; border-bottom: 1px solid #f3f4f6; }
        .totals { margin-top: 20px; padding-top: 16px; border-top: 2px solid #e5e7eb; }
        .totals-row { display: flex; justify-content: space-between; padding: 4px 0; }
        .totals-row.grand { font-size: 18px; font-weight: 700; color: #1f2937; margin-top: 8px; }
        .address { margin-top: 20px; padding: 16px; background: #f9fafb; border-radius: 6px; font-size: 14px; }
        .address h3 { margin: 0 0 8px 0; font-size: 12px; text-transform: uppercase; color: #6b7280; }
        .footer { padding: 16px 24px; font-size: 12px; color: #9ca3af; text-align: center; border-top: 1px solid #e5e7eb; }
        .btn { display: inline-block; padding: 10px 20px; background: #4f46e5; color: #fff !important; text-decoration: none; border-radius: 6px; font-weight: 500; margin-top: 16px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <div class="header">
                <h1>{{ config('app.name') }} – Order Confirmation</h1>
            </div>
            <div class="content">
                <div class="order-number">Order #{{ $order->order_number }}</div>
                <p class="thanks">Hi {{ $order->user->name ?? 'Customer' }},</p>
                <p class="thanks">Thank you for your order. We've received it and will process it shortly.</p>

                <table class="items">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th style="text-align: right;">Qty</th>
                            <th style="text-align: right;">Price</th>
                            <th style="text-align: right;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr>
                                <td>{{ $item->product_name }}</td>
                                <td style="text-align: right;">{{ $item->quantity }}</td>
                                <td style="text-align: right;">${{ number_format($item->unit_price, 2) }}</td>
                                <td style="text-align: right;">${{ number_format($item->total_price, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="totals">
                    <div class="totals-row"><span>Subtotal</span><span>${{ number_format($order->subtotal, 2) }}</span></div>
                    @if($order->discount_total > 0)
                        <div class="totals-row"><span>Discount</span><span>-${{ number_format($order->discount_total, 2) }}</span></div>
                    @endif
                    <div class="totals-row"><span>Shipping</span><span>${{ number_format($order->shipping_total, 2) }}</span></div>
                    <div class="totals-row"><span>Tax</span><span>${{ number_format($order->tax_total, 2) }}</span></div>
                    <div class="totals-row grand"><span>Total</span><span>${{ number_format($order->grand_total, 2) }}</span></div>
                </div>

                @if($order->shippingAddress)
                    <div class="address">
                        <h3>Shipping address</h3>
                        {{ $order->shippingAddress->full_name }}<br>
                        {{ $order->shippingAddress->address_line1 }}@if($order->shippingAddress->address_line2), {{ $order->shippingAddress->address_line2 }}@endif<br>
                        {{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }} {{ $order->shippingAddress->postal_code }}<br>
                        {{ $order->shippingAddress->country }}<br>
                        {{ $order->shippingAddress->phone }}
                    </div>
                @endif

                <p style="margin-top: 24px;">
                    <a href="{{ route('orders.show', $order->order_number) }}" class="btn">View order</a>
                </p>
            </div>
            <div class="footer">
                If you have any questions, please contact us. This is an automated message.
            </div>
        </div>
    </div>
</body>
</html>
