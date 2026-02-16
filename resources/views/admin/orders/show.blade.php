@extends('layouts.admin')

@section('title', 'Order ' . $order->order_number)
@section('heading', 'Order ' . $order->order_number)

@section('content')
    <div class="space-y-6">
        <a href="{{ route('admin.orders.index') }}" class="text-sm text-indigo-600 hover:underline">&larr; Back to Orders</a>

        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-5 border-b border-gray-200 bg-gray-50 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <p class="text-sm text-gray-500">Placed {{ $order->created_at->format('F d, Y \a\t g:i A') }}</p>
                    <p class="text-sm text-gray-600 mt-1">Customer: <strong>{{ $order->user?->name }}</strong> ({{ $order->user?->email }})</p>
                </div>
                <div class="flex items-center gap-3 flex-wrap">
                    <span class="px-3 py-1 rounded-full text-sm font-medium
                        @if($order->status === 'delivered') bg-green-100 text-green-800
                        @elseif($order->status === 'shipped') bg-blue-100 text-blue-800
                        @elseif($order->status === 'processing') bg-yellow-100 text-yellow-800
                        @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800
                        @endif">{{ ucfirst($order->status) }}</span>
                    <span class="px-3 py-1 rounded-full text-sm font-medium
                        @if($order->payment_status === 'paid') bg-green-100 text-green-800
                        @elseif($order->payment_status === 'failed') bg-red-100 text-red-800
                        @else bg-yellow-100 text-yellow-800
                        @endif">Payment: {{ ucfirst($order->payment_status ?? 'pending') }}</span>
                    <form method="POST" action="{{ route('admin.orders.update-status', $order) }}" class="flex items-center gap-2 flex-wrap">
                        @csrf
                        @method('PATCH')
                        <select name="status" class="rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                            <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        <select name="payment_status" class="rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Payment status</option>
                            <option value="pending" {{ ($order->payment_status ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="paid" {{ ($order->payment_status ?? '') === 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="failed" {{ ($order->payment_status ?? '') === 'failed' ? 'selected' : '' }}>Failed</option>
                            <option value="refunded" {{ ($order->payment_status ?? '') === 'refunded' ? 'selected' : '' }}>Refunded</option>
                        </select>
                        <button type="submit" class="px-3 py-1.5 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">Update</button>
                    </form>
                    <form method="POST" action="{{ route('admin.orders.destroy', $order) }}" class="inline" onsubmit="return confirm('Delete this order? This cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-3 py-1.5 bg-red-600 text-white text-sm rounded-md hover:bg-red-700">Delete order</button>
                    </form>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Items</h2>
                        <div class="space-y-4">
                            @foreach($order->items as $item)
                                <div class="flex gap-4 p-4 border border-gray-200 rounded-lg">
                                    <div class="flex-shrink-0 w-20 h-20 bg-gray-100 rounded-md overflow-hidden">
                                        @if($item->product && $item->product->images->count() > 0)
                                            <img src="{{ $item->product->images->first()->url }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-xs text-gray-400">No Image</div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-sm font-medium text-gray-900">{{ $item->product_name }}</h3>
                                        <p class="text-sm text-gray-500">SKU: {{ $item->sku }} · Qty: {{ $item->quantity }}</p>
                                        <p class="text-sm text-gray-700 mt-1">${{ number_format($item->unit_price, 2) }} each</p>
                                    </div>
                                    <div class="flex-shrink-0 text-right">
                                        <p class="text-sm font-semibold text-gray-900">${{ number_format($item->total_price, 2) }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="lg:col-span-1 space-y-6">
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Summary</h2>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between"><span class="text-gray-600">Subtotal</span><span>${{ number_format($order->subtotal, 2) }}</span></div>
                                @if($order->discount_total > 0)
                                    <div class="flex justify-between"><span class="text-gray-600">Discount</span><span class="text-green-600">-${{ number_format($order->discount_total, 2) }}</span></div>
                                @endif
                                <div class="flex justify-between"><span class="text-gray-600">Shipping</span><span>${{ number_format($order->shipping_total, 2) }}</span></div>
                                <div class="flex justify-between"><span class="text-gray-600">Tax</span><span>${{ number_format($order->tax_total, 2) }}</span></div>
                                <div class="pt-2 border-t flex justify-between font-semibold"><span>Total</span><span>${{ number_format($order->grand_total, 2) }}</span></div>
                            </div>
                            @if($order->payment_method)
                                <p class="mt-4 text-sm text-gray-600"><span class="font-medium">Payment:</span> {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</p>
                            @endif
                        </div>

                        @if($order->shippingAddress)
                            <div class="bg-gray-50 rounded-lg p-6">
                                <h3 class="text-sm font-semibold text-gray-900 mb-2">Shipping Address</h3>
                                <div class="text-sm text-gray-600">
                                    <p class="font-medium">{{ $order->shippingAddress->full_name }}</p>
                                    <p>{{ $order->shippingAddress->address_line1 }}{{ $order->shippingAddress->address_line2 ? ', ' . $order->shippingAddress->address_line2 : '' }}</p>
                                    <p>{{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }} {{ $order->shippingAddress->postal_code }}</p>
                                    <p>{{ $order->shippingAddress->country }}</p>
                                    <p class="mt-1">{{ $order->shippingAddress->phone }}</p>
                                </div>
                            </div>
                        @endif

                        @if($order->billingAddress && $order->billing_address_id != $order->shipping_address_id)
                            <div class="bg-gray-50 rounded-lg p-6">
                                <h3 class="text-sm font-semibold text-gray-900 mb-2">Billing Address</h3>
                                <div class="text-sm text-gray-600">
                                    <p class="font-medium">{{ $order->billingAddress->full_name }}</p>
                                    <p>{{ $order->billingAddress->address_line1 }}{{ $order->billingAddress->address_line2 ? ', ' . $order->billingAddress->address_line2 : '' }}</p>
                                    <p>{{ $order->billingAddress->city }}, {{ $order->billingAddress->state }} {{ $order->billingAddress->postal_code }}</p>
                                    <p>{{ $order->billingAddress->country }}</p>
                                    <p class="mt-1">{{ $order->billingAddress->phone }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
