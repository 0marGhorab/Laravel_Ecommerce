<div>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: #fff; }
            .print\:block { display: block !important; }
        }
    </style>
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="no-print flex items-center justify-between mb-6">
            <a href="{{ route('orders.index') }}" class="text-sm text-indigo-600 hover:underline">
                &larr; Back to Orders
            </a>
            <button type="button" onclick="window.print()" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
                Print order
            </button>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <!-- Order Header -->
            <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Order #{{ $order->order_number }}</h1>
                        <p class="text-sm text-gray-500 mt-1">
                            Placed on {{ $order->created_at->format('F d, Y') }} at {{ $order->created_at->format('g:i A') }}
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1 rounded-full text-sm font-medium
                            @if($order->status === 'delivered') bg-green-100 text-green-800
                            @elseif($order->status === 'shipped') bg-blue-100 text-blue-800
                            @elseif($order->status === 'processing') bg-yellow-100 text-yellow-800
                            @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                            @elseif($order->status === 'refunded') bg-gray-100 text-gray-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                        </span>
                        <span class="px-3 py-1 rounded-full text-sm font-medium
                            @if($order->payment_status === 'paid') bg-green-100 text-green-800
                            @elseif($order->payment_status === 'failed') bg-red-100 text-red-800
                            @elseif($order->payment_status === 'refunded') bg-gray-100 text-gray-800
                            @else bg-yellow-100 text-yellow-800
                            @endif">
                            Payment: {{ ucfirst($order->payment_status) }}
                        </span>
                        <a href="{{ route('orders.invoice', $order->order_number) }}" target="_blank" class="no-print text-sm font-medium text-indigo-600 hover:text-indigo-800">
                            Download invoice (PDF)
                        </a>
                        <button type="button" wire:click="reorder" class="no-print text-sm font-medium text-indigo-600 hover:text-indigo-800">
                            Buy again
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tracking & timeline -->
            @if($order->tracking_number || in_array($order->status, ['processing', 'shipped', 'delivered'], true))
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/70">
                    @if($order->tracking_number)
                        <p class="text-sm text-gray-700">
                            <span class="font-medium">Tracking:</span>
                            <a href="https://www.google.com/search?q={{ urlencode($order->tracking_number) }}" target="_blank" rel="noopener noreferrer" class="text-indigo-600 hover:underline font-mono">{{ $order->tracking_number }}</a>
                            <span class="text-gray-500 ml-1">(search carrier)</span>
                        </p>
                    @endif
                    <div class="mt-3 flex flex-wrap items-center gap-4 text-sm">
                        <span class="flex items-center gap-1.5 {{ in_array($order->status, ['pending','processing','shipped','delivered'], true) ? 'text-green-600' : 'text-gray-400' }}">
                            <span class="w-2 h-2 rounded-full {{ in_array($order->status, ['pending','processing','shipped','delivered'], true) ? 'bg-green-500' : 'bg-gray-300' }}"></span>
                            Order placed {{ $order->created_at->format('M j, Y') }}
                        </span>
                        <span class="flex items-center gap-1.5 {{ in_array($order->status, ['processing','shipped','delivered'], true) ? 'text-green-600' : 'text-gray-400' }}">
                            <span class="w-2 h-2 rounded-full {{ in_array($order->status, ['processing','shipped','delivered'], true) ? 'bg-green-500' : 'bg-gray-300' }}"></span>
                            Processing
                        </span>
                        <span class="flex items-center gap-1.5 {{ in_array($order->status, ['shipped','delivered'], true) ? 'text-green-600' : 'text-gray-400' }}">
                            <span class="w-2 h-2 rounded-full {{ in_array($order->status, ['shipped','delivered'], true) ? 'bg-green-500' : 'bg-gray-300' }}"></span>
                            Shipped{{ $order->shipped_at ? ' ' . $order->shipped_at->format('M j, Y') : '' }}
                        </span>
                        <span class="flex items-center gap-1.5 {{ $order->status === 'delivered' ? 'text-green-600' : 'text-gray-400' }}">
                            <span class="w-2 h-2 rounded-full {{ $order->status === 'delivered' ? 'bg-green-500' : 'bg-gray-300' }}"></span>
                            Delivered
                        </span>
                    </div>
                </div>
            @endif

            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Order Items -->
                    <div class="lg:col-span-2">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Items</h2>
                        <div class="space-y-4">
                            @foreach($order->items as $item)
                                <div class="flex gap-4 p-4 border border-gray-200 rounded-lg">
                                    <!-- Product Image -->
                                    <div class="flex-shrink-0 w-20 h-20 bg-gray-100 rounded-md overflow-hidden">
                                        @if($item->product && $item->product->images->count() > 0)
                                            <img 
                                                src="{{ $item->product->images->first()->url }}"
                                                alt="{{ $item->product_name }}"
                                                class="w-full h-full object-cover"
                                            />
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-xs text-gray-400">
                                                No Image
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Product Details -->
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-sm font-medium text-gray-900">
                                            {{ $item->product_name }}
                                        </h3>
                                        <p class="text-sm text-gray-500 mt-1">SKU: {{ $item->sku }}</p>
                                        <p class="text-sm text-gray-500">Quantity: {{ $item->quantity }}</p>
                                        <div class="mt-2">
                                            <span class="text-sm font-medium text-gray-900">
                                                ${{ number_format($item->unit_price, 2) }} each
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Item Total -->
                                    <div class="flex-shrink-0 text-right">
                                        <p class="text-sm font-semibold text-gray-900">
                                            ${{ number_format($item->total_price, 2) }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="lg:col-span-1">
                        <div class="bg-gray-50 rounded-lg p-6 space-y-4">
                            <h2 class="text-lg font-semibold text-gray-900">Order Summary</h2>

                            <!-- Shipping Address -->
                            @if($order->shippingAddress)
                                <div>
                                    <h3 class="text-sm font-medium text-gray-900 mb-2">Shipping Address</h3>
                                    <div class="text-sm text-gray-600">
                                        <p class="font-medium">{{ $order->shippingAddress->full_name }}</p>
                                        <p>{{ $order->shippingAddress->address_line1 }}</p>
                                        @if($order->shippingAddress->address_line2)
                                            <p>{{ $order->shippingAddress->address_line2 }}</p>
                                        @endif
                                        <p>{{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }} {{ $order->shippingAddress->postal_code }}</p>
                                        <p>{{ $order->shippingAddress->country }}</p>
                                        <p class="mt-1">{{ $order->shippingAddress->phone }}</p>
                                    </div>
                                </div>
                            @endif

                            <!-- Billing Address -->
                            @if($order->billingAddress && $order->billing_address_id !== $order->shipping_address_id)
                                <div class="pt-4 border-t border-gray-200">
                                    <h3 class="text-sm font-medium text-gray-900 mb-2">Billing Address</h3>
                                    <div class="text-sm text-gray-600">
                                        <p class="font-medium">{{ $order->billingAddress->full_name }}</p>
                                        <p>{{ $order->billingAddress->address_line1 }}</p>
                                        @if($order->billingAddress->address_line2)
                                            <p>{{ $order->billingAddress->address_line2 }}</p>
                                        @endif
                                        <p>{{ $order->billingAddress->city }}, {{ $order->billingAddress->state }} {{ $order->billingAddress->postal_code }}</p>
                                        <p>{{ $order->billingAddress->country }}</p>
                                        <p class="mt-1">{{ $order->billingAddress->phone }}</p>
                                    </div>
                                </div>
                            @endif

                            <!-- Order Totals -->
                            <div class="pt-4 border-t border-gray-200 space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Subtotal</span>
                                    <span class="text-gray-900">${{ number_format($order->subtotal, 2) }}</span>
                                </div>
                                @if($order->discount_total > 0)
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Discount{{ $order->coupon_code ? ' (' . $order->coupon_code . ')' : '' }}</span>
                                        <span class="text-green-600">-${{ number_format($order->discount_total, 2) }}</span>
                                    </div>
                                @endif
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Shipping</span>
                                    <span class="text-gray-900">${{ number_format($order->shipping_total, 2) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Tax</span>
                                    <span class="text-gray-900">${{ number_format($order->tax_total, 2) }}</span>
                                </div>
                                <div class="pt-2 border-t border-gray-200 flex justify-between">
                                    <span class="text-base font-semibold text-gray-900">Total</span>
                                    <span class="text-base font-semibold text-gray-900">${{ number_format($order->grand_total, 2) }}</span>
                                </div>
                            </div>

                            <!-- Payment Method -->
                            @if($order->payment_method)
                                <div class="pt-4 border-t border-gray-200">
                                    <p class="text-sm text-gray-600">
                                        <span class="font-medium">Payment Method:</span>
                                        {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
