<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order status update</title>
    <style>
        body { font-family: sans-serif; line-height: 1.6; color: #374151; margin: 0; padding: 0; background: #f3f4f6; }
        .wrapper { max-width: 600px; margin: 0 auto; padding: 24px; }
        .card { background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden; }
        .header { background: #4f46e5; color: #fff; padding: 20px 24px; }
        .header h1 { margin: 0; font-size: 20px; font-weight: 600; }
        .content { padding: 24px; }
        .status { display: inline-block; padding: 6px 12px; border-radius: 6px; font-weight: 600; font-size: 14px; margin: 8px 0; }
        .status.shipped { background: #dbeafe; color: #1d4ed8; }
        .status.delivered { background: #d1fae5; color: #047857; }
        .status.processing { background: #fef3c7; color: #b45309; }
        .status.cancelled { background: #fee2e2; color: #b91c1c; }
        .status.pending { background: #f3f4f6; color: #4b5563; }
        .btn { display: inline-block; padding: 10px 20px; background: #4f46e5; color: #fff !important; text-decoration: none; border-radius: 6px; font-weight: 500; margin-top: 16px; }
        .footer { padding: 16px 24px; font-size: 12px; color: #9ca3af; text-align: center; border-top: 1px solid #e5e7eb; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <div class="header">
                <h1>{{ config('app.name') }} – Order update</h1>
            </div>
            <div class="content">
                <p>Hi {{ $order->user->name ?? 'Customer' }},</p>
                <p>Your order <strong>#{{ $order->order_number }}</strong> has been updated.</p>
                <p>
                    Status: <span class="status {{ $order->status }}">{{ ucfirst($order->status) }}</span>
                </p>
                <p>If you have any questions, please contact us.</p>
                <p>
                    <a href="{{ route('orders.show', $order->order_number) }}" class="btn">View order</a>
                </p>
            </div>
            <div class="footer">
                This is an automated message.
            </div>
        </div>
    </div>
</body>
</html>
