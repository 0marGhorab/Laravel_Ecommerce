<div>
    <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-semibold text-warm-darker mb-6 animate-fade-in">Shopping Cart</h1>

        @if ($cart->items->count())
            <div class="space-y-4">
                @foreach ($cart->items as $item)
                    <div class="card-cozy flex items-center justify-between p-5 animate-fade-in-up">
                        <div>
                            <div class="font-medium text-warm-darker">
                                {{ $item->product?->name ?? 'Product removed' }}
                            </div>
                            <div class="text-sm text-warm/70">
                                {{ $item->product?->sku }}
                            </div>
                            <div class="text-sm text-warm/80 mt-1">
                                ${{ number_format($item->unit_price, 2) }} each
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <div class="flex items-center border border-cream-300 rounded-lg">
                                <button wire:click="decrementItem({{ $item->id }})"
                                        wire:loading.attr="disabled"
                                        wire:loading.class="opacity-50 cursor-wait"
                                        class="px-2 py-1 text-warm hover:bg-cream-50 rounded-l transition">
                                    -
                                </button>
                                <div class="px-3 py-1 border-x border-cream-300">
                                    {{ $item->quantity }}
                                </div>
                                <button wire:click="incrementItem({{ $item->id }})"
                                        wire:loading.attr="disabled"
                                        wire:loading.class="opacity-50 cursor-wait"
                                        class="px-2 py-1 text-warm hover:bg-cream-50 rounded-r transition">
                                    +
                                </button>
                            </div>

                            <div class="font-semibold text-warm-darker">
                                ${{ number_format($item->total_price, 2) }}
                            </div>

                            <button wire:click="removeItem({{ $item->id }})"
                                    wire:loading.attr="disabled"
                                    wire:loading.class="opacity-50 cursor-wait"
                                    class="text-sm text-red-500 hover:underline transition">
                                Remove
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 animate-fade-in">
                <a href="{{ route('products.index') }}" class="text-sm text-warm hover:text-warm-dark font-medium transition">
                    ← Continue Shopping
                </a>
                <div class="flex flex-col sm:flex-row items-end sm:items-center gap-4">
                    <div class="text-right">
                        <div class="text-sm text-warm/70">Subtotal</div>
                        <div class="text-2xl font-bold text-warm-darker">
                            ${{ number_format($this->subtotal, 2) }}
                        </div>
                    </div>
                    <a href="{{ route('checkout.index') }}" 
                       class="btn-cozy px-6 py-3">
                        Proceed to Checkout
                    </a>
                </div>
            </div>
        @else
            <x-empty-state
                title="Your cart is empty"
                description="Add items from the store to see them here."
                ctaText="Browse products"
                ctaUrl="{{ route('products.index') }}"
            >
                <x-slot:icon>
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l3-8H6.4M7 13L5.4 5M7 13l-2 5m5 5a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm7 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2z" />
                    </svg>
                </x-slot:icon>
            </x-empty-state>
        @endif
    </div>
</div>
