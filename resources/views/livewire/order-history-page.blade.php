<div>
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-semibold text-warm-darker mb-6 animate-fade-in">My Orders</h1>

        @if ($orders->count())
            <div class="space-y-4">
                @foreach ($orders as $order)
                    <div class="card-cozy overflow-hidden animate-fade-in-up">
                        <div class="p-6">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-4 mb-2">
                                        <h3 class="text-lg font-semibold text-warm-darker">
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
                                    <p class="text-sm text-warm/70">
                                        Placed on {{ $order->created_at->format('M d, Y') }} at {{ $order->created_at->format('g:i A') }}
                                    </p>
                                    <p class="text-sm text-warm/80 mt-1">
                                        {{ $order->items->count() }} {{ $order->items->count() === 1 ? 'item' : 'items' }} • Total: ${{ number_format($order->grand_total, 2) }}
                                    </p>
                                </div>

                                <div class="flex items-center gap-3">
                                    <a 
                                        href="{{ route('orders.show', $order->order_number) }}"
                                        class="btn-cozy-soft text-sm"
                                    >
                                        View Details
                                    </a>
                                </div>
                            </div>

                            <div class="mt-4 pt-4 border-t border-cream-200">
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
            <x-empty-state
                title="No orders yet"
                description="When you place an order, it will appear here."
                ctaText="Start shopping"
                ctaUrl="{{ route('products.index') }}"
            >
                <x-slot:icon>
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </x-slot:icon>
            </x-empty-state>
        @endif
    </div>
</div>
