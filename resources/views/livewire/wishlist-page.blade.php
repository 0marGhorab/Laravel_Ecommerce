<div>
    <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-semibold mb-6">My Wishlist</h1>

        @if (!auth()->check())
            <div class="bg-white rounded-lg shadow-lg p-8 text-center">
                <div class="max-w-md mx-auto">
                    <div class="mb-4">
                        <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 0 1 6.364 0L12 7.636l1.318-1.318a4.5 4.5 0 1 1 6.364 6.364L12 21.364l-7.682-7.682a4.5 4.5 0 0 1 0-6.364z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-semibold text-gray-900 mb-2">Login Required</h2>
                    <p class="text-gray-600 mb-6">
                        You need to login first to use the wishlist page. Create an account or login to save your favorite products.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                        <a href="{{ route('login') }}" 
                           class="inline-flex items-center justify-center px-6 py-3 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition">
                            Login
                        </a>
                        <a href="{{ route('register') }}" 
                           class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-md hover:bg-gray-50 transition">
                            Create Account
                        </a>
                    </div>
                </div>
            </div>
        @elseif ($wishlist && $wishlist->items->count())
            <div class="space-y-4">
                @foreach ($wishlist->items as $item)
                    <div class="flex items-center justify-between bg-white p-4 rounded-lg shadow-sm">
                        <div>
                            <a href="{{ route('products.show', $item->product?->slug ?? '#') }}"
                               class="font-medium text-gray-900 hover:text-indigo-600">
                                {{ $item->product?->name ?? 'Product removed' }}
                            </a>
                            @if ($item->product)
                                <div class="text-sm text-gray-500">
                                    {{ $item->product->category?->name ?? 'Uncategorized' }}
                                </div>
                                <div class="text-sm text-gray-700 mt-1">
                                    ${{ number_format($item->product->price, 2) }}
                                </div>
                            @endif
                        </div>

                        <div class="flex items-center gap-3">
                            @if ($item->product)
                                @if(in_array($item->product->id, $cartProductIds))
                                    <button
                                        disabled
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-600 text-white text-xs font-medium rounded-md cursor-not-allowed opacity-75"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span>Added to Cart</span>
                                    </button>
                                @else
                                    <button
                                        wire:click="addToCart({{ $item->product->id }})"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 text-white text-xs font-medium rounded-md hover:bg-indigo-700 transition"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l3-8H6.4M7 13L5.4 5M7 13l-2 5m5 5a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm7 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2z" />
                                        </svg>
                                        <span>Add to Cart</span>
                                    </button>
                                @endif

                                <a href="{{ route('products.show', $item->product->slug) }}"
                                   class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-gray-600 text-xs font-medium rounded-md hover:bg-gray-50 transition">
                                    View
                                </a>
                            @endif

                            <button
                                wire:click="removeItem({{ $item->id }})"
                                class="text-xs text-red-600 hover:underline"
                            >
                                Remove
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @elseif ($wishlist && $wishlist->items->count() === 0)
            <p class="text-gray-500">Your wishlist is empty.</p>
        @endif
    </div>
</div>
