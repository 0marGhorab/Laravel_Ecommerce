<div>
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <a href="{{ route('products.index') }}" class="text-sm text-warm hover:text-warm-dark font-medium transition inline-block mb-2">&larr; Back to products</a>

        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="animate-fade-in">
                @if ($product->images->count())
                    <div class="mb-4" x-data="{ selectedImage: 0 }">
                        <div class="aspect-[4/3] bg-cream-100 rounded-xl overflow-hidden relative mb-4 shadow-cozy">
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
                                    x-bind:class="selectedImage === {{ $index }} ? 'ring-2 ring-warm ring-offset-2' : 'ring-1 ring-cream-300'"
                                    class="flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden focus:outline-none transition"
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
                    <div class="aspect-[4/3] bg-cream-100 rounded-xl flex items-center justify-center text-warm/60 text-sm mb-4">
                        No Image Available
                    </div>
                @endif
            </div>

            <div class="space-y-4 animate-fade-in-up">
                <div class="text-sm text-warm/70">
                    {{ $product->category?->name ?? 'Uncategorized' }}
                </div>

                <h1 class="text-2xl font-bold text-warm-darker">
                    {{ $product->name }}
                </h1>

                <div class="flex flex-wrap items-center gap-3">
                    <div class="text-warm-dark text-xl font-semibold">
                        ${{ number_format($product->price, 2) }}
                    </div>
                    @if($product->reviews_count > 0)
                        <div class="flex items-center gap-1.5 text-sm text-warm/80">
                            @php $avg = $product->average_rating; @endphp
                            <span class="flex items-center" aria-label="{{ $avg }} out of 5 stars">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= floor($avg))
                                        <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    @else
                                        <svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    @endif
                                @endfor
                            </span>
                            <span>{{ $avg }}</span>
                            <span class="text-warm/70">({{ $product->reviews_count }} {{ $product->reviews_count === 1 ? 'review' : 'reviews' }})</span>
                        </div>
                    @endif
                </div>

                <div class="flex items-center gap-3 mt-4">
                    @if ($cartQuantity > 0)
                        <!-- Counter -->
                        <div class="flex items-center border border-cream-300 rounded-lg">
                            <button
                                wire:click="decrementProduct"
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-50 cursor-wait"
                                class="px-3 py-2 text-warm hover:bg-cream-50 transition rounded-l"
                            >
                                -
                            </button>
                            <div class="px-4 py-2 border-x border-cream-300 text-sm font-medium text-warm-darker">
                                {{ $cartQuantity }}
                            </div>
                            <button
                                wire:click="incrementProduct"
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-50 cursor-wait"
                                class="px-3 py-2 text-warm hover:bg-cream-50 transition rounded-r"
                            >
                                +
                            </button>
                        </div>
                    @else
                        <button
                            wire:click="addToCart"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-70 cursor-wait"
                            class="btn-cozy inline-flex items-center gap-2"
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
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-60 cursor-wait"
                            class="inline-flex items-center gap-2 px-4 py-2 border border-red-400 text-red-500 text-sm font-medium rounded-lg hover:bg-red-50 transition"
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
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-60 cursor-wait"
                            class="btn-cozy-soft inline-flex items-center gap-2 hover:border-red-400/50 hover:text-red-500"
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
                    <p class="text-warm/90">
                        {{ $product->short_description }}
                    </p>
                @endif

                @if ($product->long_description)
                    <div class="prose max-w-none text-warm/90">
                        {!! nl2br(e($product->long_description)) !!}
                    </div>
                @endif
            </div>
        </div>

        <!-- Reviews Section -->
        <div class="mt-12 pt-8 border-t border-cream-200 animate-fade-in">
            <h2 class="text-lg font-semibold text-warm-darker mb-4">Customer reviews</h2>

            @if($canReview)
                <div class="bg-cream-50 rounded-xl p-6 mb-8 border border-cream-200">
                    <h3 class="text-sm font-medium text-warm-darker mb-3">Write a review</h3>
                    <form wire:submit="submitReview" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Rating</label>
                            <div class="flex gap-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button"
                                            wire:click="$set('rating', {{ $i }})"
                                            class="p-0.5 focus:outline-none focus:ring-2 focus:ring-amber-400 rounded"
                                            aria-label="{{ $i }} star{{ $i > 1 ? 's' : '' }}">
                                        <svg class="w-8 h-8 transition {{ $i <= $rating ? 'text-amber-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    </button>
                                @endfor
                            </div>
                        </div>
                        <div>
                            <label for="reviewTitle" class="block text-sm font-medium text-gray-700 mb-1">Title (optional)</label>
                            <input type="text" id="reviewTitle" wire:model="reviewTitle" maxlength="255"
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                   placeholder="Sum up your experience">
                        </div>
                        <div>
                            <label for="reviewBody" class="block text-sm font-medium text-gray-700 mb-1">Review (optional)</label>
                            <textarea id="reviewBody" wire:model="reviewBody" rows="3" maxlength="2000"
                                      class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                      placeholder="Share your thoughts about this product"></textarea>
                        </div>
                        <button type="submit"
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-70 cursor-wait"
                                class="btn-cozy">
                            Submit review
                        </button>
                        @error('review')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </form>
                </div>
            @elseif($userReview)
                <p class="text-sm text-gray-600 mb-4">You reviewed this product.</p>
            @elseif(!auth()->check())
                <p class="text-sm text-warm/80 mb-4"><a href="{{ route('login') }}" wire:navigate class="text-warm hover:underline font-medium">Log in</a> to leave a review.</p>
            @endif

            @if($reviews->isEmpty())
                <p class="text-warm/70 text-sm">No reviews yet. Be the first to review this product!</p>
            @else
                <ul class="space-y-6">
                    @foreach($reviews as $review)
                        <li class="border-b border-cream-200 pb-6 last:border-0 last:pb-0">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex items-center gap-2">
                                    <span class="font-medium text-warm-darker">{{ $review->user->name ?? 'Anonymous' }}</span>
                                    <span class="flex items-center text-amber-400" aria-label="{{ $review->rating }} out of 5">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-4 h-4" fill="{{ $i <= $review->rating ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        @endfor
                                    </span>
                                </div>
                                <time class="text-xs text-warm/60" datetime="{{ $review->created_at->toIso8601String() }}">{{ $review->created_at->format('M j, Y') }}</time>
                            </div>
                            @if($review->title)
                                <h4 class="mt-1 font-medium text-warm-darker">{{ $review->title }}</h4>
                            @endif
                            @if($review->body)
                                <p class="mt-1 text-sm text-warm/80">{{ $review->body }}</p>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        @if($recommendedProducts->isNotEmpty())
            <div class="mt-12 pt-8 border-t border-cream-200">
                <h2 class="text-lg font-semibold text-warm-darker mb-4">You might also like</h2>
                <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach($recommendedProducts as $rec)
                        <a href="{{ route('products.show', $rec->slug) }}" class="group block card-cozy overflow-hidden">
                            <div class="aspect-square bg-cream-100 overflow-hidden">
                                @if($rec->images->isNotEmpty())
                                    <img src="{{ $rec->images->first()->url }}" alt="{{ $rec->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-warm/50 text-sm">No image</div>
                                @endif
                            </div>
                            <div class="p-3">
                                <h3 class="text-sm font-medium text-warm-darker line-clamp-2 group-hover:text-warm-dark transition-colors">{{ $rec->name }}</h3>
                                <p class="mt-1 text-warm-dark font-semibold">${{ number_format($rec->price, 2) }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
