<div>
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <a href="{{ route('products.index') }}" class="text-sm text-indigo-600 hover:underline">&larr; Back to products</a>

        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                @if ($product->images->count())
                    <!-- Main Image Display -->
                    <div class="mb-4" x-data="{ selectedImage: 0 }">
                        <!-- Large Main Image -->
                        <div class="aspect-[4/3] bg-gray-100 rounded-lg overflow-hidden relative mb-4">
                            @foreach($product->images as $index => $image)
                                <div 
                                    x-show="selectedImage === {{ $index }}"
                                    x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0"
                                    x-transition:enter-end="opacity-100"
                                    x-transition:leave="transition ease-in duration-200"
                                    x-transition:leave-start="opacity-100"
                                    x-transition:leave-end="opacity-0"
                                    class="absolute inset-0"
                                    style="display: {{ $index === 0 ? 'block' : 'none' }};"
                                >
                                    <img 
                                        src="{{ $image->url }}"
                                        alt="{{ $product->name }} - Image {{ $index + 1 }}"
                                        class="w-full h-full object-cover"
                                        loading="{{ $index === 0 ? 'eager' : 'lazy' }}"
                                        onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'400\' height=\'300\'%3E%3Crect fill=\'%23f3f4f6\' width=\'400\' height=\'300\'/%3E%3Ctext fill=\'%239ca3af\' font-family=\'sans-serif\' font-size=\'14\' x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dominant-baseline=\'middle\'%3ENo Image%3C/text%3E%3C/svg%3E'"
                                    />
                                </div>
                            @endforeach
                        </div>

                        <!-- Thumbnail Images - Always show all thumbnails -->
                        <div class="flex gap-2 overflow-x-auto pb-2">
                            @foreach($product->images as $index => $image)
                                <button
                                    type="button"
                                    @click="selectedImage = {{ $index }}"
                                    x-bind:class="selectedImage === {{ $index }} ? 'ring-2 ring-indigo-600 ring-offset-2' : 'ring-1 ring-gray-300'"
                                    class="flex-shrink-0 w-20 h-20 rounded-md overflow-hidden focus:outline-none transition"
                                    aria-label="View image {{ $index + 1 }}"
                                >
                                    <img 
                                        src="{{ $image->url }}"
                                        alt="{{ $product->name }} - Thumbnail {{ $index + 1 }}"
                                        class="w-full h-full object-cover"
                                        loading="lazy"
                                        onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'80\' height=\'80\'%3E%3Crect fill=\'%23f3f4f6\' width=\'80\' height=\'80\'/%3E%3Ctext fill=\'%239ca3af\' font-family=\'sans-serif\' font-size=\'10\' x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dominant-baseline=\'middle\'%3ENo Image%3C/text%3E%3C/svg%3E'"
                                    />
                                </button>
                            @endforeach
                        </div>
                    </div>
                @else
                    <!-- Placeholder when no images -->
                    <div class="aspect-[4/3] bg-gray-100 rounded-lg flex items-center justify-center text-gray-400 text-sm mb-4">
                        No Image Available
                    </div>
                @endif
            </div>

            <div class="space-y-4">
                <div class="text-sm text-gray-500">
                    {{ $product->category?->name ?? 'Uncategorized' }}
                </div>

                <h1 class="text-2xl font-bold text-gray-900">
                    {{ $product->name }}
                </h1>

                <div class="text-indigo-600 text-xl font-semibold">
                    ${{ number_format($product->price, 2) }}
                </div>

                <div class="flex items-center gap-3 mt-4">
                    @if ($cartQuantity > 0)
                        <!-- Counter -->
                        <div class="flex items-center border rounded">
                            <button
                                wire:click="decrementProduct"
                                class="px-3 py-2 text-gray-600 hover:bg-gray-100"
                            >
                                -
                            </button>
                            <div class="px-4 py-2 border-x text-sm font-medium">
                                {{ $cartQuantity }}
                            </div>
                            <button
                                wire:click="incrementProduct"
                                class="px-3 py-2 text-gray-600 hover:bg-gray-100"
                            >
                                +
                            </button>
                        </div>
                    @else
                        <!-- Add to Cart Button -->
                        <button
                            wire:click="addToCart"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l3-8H6.4M7 13L5.4 5M7 13l-2 5m5 5a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm7 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2z" />
                            </svg>
                            <span>Add to Cart</span>
                        </button>
                    @endif

                    @if($isInWishlist)
                        <button
                            wire:click="addToWishlist"
                            class="inline-flex items-center gap-2 px-4 py-2 border border-red-600 text-red-600 text-sm font-medium rounded-md hover:bg-red-50 transition"
                            title="Remove from Wishlist"
                        >
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                            </svg>
                            <span>Remove from Wishlist</span>
                        </button>
                    @else
                        <button
                            wire:click="addToWishlist"
                            class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-600 text-sm font-medium rounded-md hover:border-red-600 hover:text-red-600 hover:bg-red-50 transition"
                            title="Add to Wishlist"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 0 1 6.364 0L12 7.636l1.318-1.318a4.5 4.5 0 1 1 6.364 6.364L12 21.364l-7.682-7.682a4.5 4.5 0 0 1 0-6.364z" />
                            </svg>
                            <span>Add to Wishlist</span>
                        </button>
                    @endif
                </div>

                @if ($product->short_description)
                    <p class="text-gray-700">
                        {{ $product->short_description }}
                    </p>
                @endif

                @if ($product->long_description)
                    <div class="prose max-w-none">
                        {!! nl2br(e($product->long_description)) !!}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
