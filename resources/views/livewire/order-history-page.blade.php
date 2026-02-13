<div>
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-semibold mb-6">My Orders</h1>

        @if ($orders->count())
            <div class="space-y-4">
                @foreach ($orders as $order)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition">
                        <div class="p-6">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                <!-- Order Info -->
                                <div class="flex-1">
                                    <div class="flex items-center gap-4 mb-2">
                                        <h3 class="text-lg font-semibold text-gray-900">
                                            Order #{{ $order->order_number }}
                                        </h3>
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($order->status === 'delivered') bg-green-100 text-green-800
                                            @elseif($order->status === 'shipped') bg-blue-100 text-blue-800
                                            @elseif($order->status === 'processing') bg-yellow-100 text-yellow-800
                                            @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                            @elseif($order->status === 'refunded') bg-gray-100 text-gray-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-500">
                                        Placed on {{ $order->created_at->format('M d, Y') }} at {{ $order->created_at->format('g:i A') }}
                                    </p>
                                    <p class="text-sm text-gray-600 mt-1">
                                        {{ $order->items->count() }} {{ $order->items->count() === 1 ? 'item' : 'items' }} • Total: ${{ number_format($order->grand_total, 2) }}
                                    </p>
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center gap-3">
                                    <a 
                                        href="{{ route('orders.show', $order->order_number) }}"
                                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition"
                                    >
                                        View Details
                                    </a>
                                </div>
                            </div>

                            <!-- Order Items Preview -->
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <div class="flex gap-4 overflow-x-auto">
                                    @foreach($order->items->take(4) as $item)
                                        <div class="flex-shrink-0 w-16 h-16 bg-gray-100 rounded-md overflow-hidden">
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
                                    @endforeach
                                    @if($order->items->count() > 4)
                                        <div class="flex-shrink-0 w-16 h-16 bg-gray-100 rounded-md flex items-center justify-center text-xs text-gray-500 font-medium">
                                            +{{ $order->items->count() - 4 }} more
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $orders->links() }}
            </div>
        @else
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No orders yet</h3>
                <p class="mt-2 text-sm text-gray-500">When you place an order, it will appear here.</p>
                <div class="mt-6">
                    <a 
                        href="{{ route('products.index') }}"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 transition"
                    >
                        Start Shopping
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
