<div>
    <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-semibold mb-6">Shopping Cart</h1>

        @if ($cart->items->count())
            <div class="space-y-4">
                @foreach ($cart->items as $item)
                    <div class="flex items-center justify-between bg-white p-4 rounded-lg shadow-sm">
                        <div>
                            <div class="font-medium text-gray-900">
                                {{ $item->product?->name ?? 'Product removed' }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $item->product?->sku }}
                            </div>
                            <div class="text-sm text-gray-700 mt-1">
                                ${{ number_format($item->unit_price, 2) }} each
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <div class="flex items-center border rounded">
                                <button wire:click="decrementItem({{ $item->id }})" class="px-2 py-1 text-gray-600">
                                    -
                                </button>
                                <div class="px-3 py-1 border-x">
                                    {{ $item->quantity }}
                                </div>
                                <button wire:click="incrementItem({{ $item->id }})" class="px-2 py-1 text-gray-600">
                                    +
                                </button>
                            </div>

                            <div class="font-semibold text-gray-900">
                                ${{ number_format($item->total_price, 2) }}
                            </div>

                            <button wire:click="removeItem({{ $item->id }})"
                                    class="text-sm text-red-600 hover:underline">
                                Remove
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <a href="{{ route('products.index') }}" class="text-sm text-indigo-600 hover:underline">
                    ← Continue Shopping
                </a>
                <div class="flex flex-col sm:flex-row items-end sm:items-center gap-4">
                    <div class="text-right">
                        <div class="text-sm text-gray-500">Subtotal</div>
                        <div class="text-2xl font-bold text-gray-900">
                            ${{ number_format($this->subtotal, 2) }}
                        </div>
                    </div>
                    <a href="{{ route('checkout.index') }}" 
                       class="inline-flex items-center justify-center px-6 py-3 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition">
                        Proceed to Checkout
                    </a>
                </div>
            </div>
        @else
            <p class="text-gray-500">Your cart is empty.</p>
        @endif
    </div>
</div>
