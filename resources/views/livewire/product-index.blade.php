<div>
    {{-- Promo banner slideshow at top (homepage + product list) --}}
    @if(isset($banners) && $banners->isNotEmpty())
        <div class="w-full left-0 right-0 relative h-48 sm:h-56 md:h-64 overflow-hidden bg-gray-800" x-data="{ current: 0, total: {{ $banners->count() }} }" x-init="setInterval(() => { current = (current + 1) % total }, 5000)">
            @foreach($banners as $i => $banner)
                <div x-show="current === {{ $i }}"
                     x-transition:enter="transition ease-out duration-500"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="absolute inset-0 bg-cover bg-center bg-no-repeat {{ $banner->image_url ? '' : 'bg-gray-800' }}"
                     style="@if($banner->image_url) background-image: url('{{ $banner->image_url }}'); @endif {{ $loop->first ? '' : 'display: none;' }}"
                >
                    <div class="absolute inset-0 bg-black/40 flex items-center justify-between px-6 sm:px-10 md:px-16 text-white">
                        <div class="max-w-2xl">
                            @if($banner->title)
                                <h2 class="text-xl sm:text-2xl md:text-4xl font-bold drop-shadow">{{ $banner->title }}</h2>
                            @endif
                            @if($banner->subtitle)
                                <p class="mt-2 text-white/95 text-sm sm:text-base md:text-lg drop-shadow">{{ $banner->subtitle }}</p>
                            @endif
                            @if($banner->cta_text && $banner->cta_url)
                                <a href="{{ $banner->cta_url }}" class="mt-4 inline-block px-5 py-2.5 bg-white text-gray-900 font-medium rounded-lg hover:bg-gray-100 transition shadow">
                                    {{ $banner->cta_text }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
            @if($banners->count() > 1)
                <div class="absolute bottom-3 left-0 right-0 flex justify-center gap-2 z-10">
                    @foreach($banners as $i => $banner)
                        <button type="button" @click="current = {{ $i }}"
                                class="w-2.5 h-2.5 rounded-full transition"
                                :class="current === {{ $i }} ? 'bg-white scale-110' : 'bg-white/50 hover:bg-white/70'"
                                aria-label="Go to slide {{ $i + 1 }}"></button>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    <!-- Category Carousel Bar -->
    @if($categories->count() > 0)
        <div class="bg-white/95 backdrop-blur-sm border-b border-cream-200 sticky top-0 z-40 shadow-cozy"
             x-data="{
                 scrollContainer: null,
                 canScrollLeft: false,
                 canScrollRight: false,
                 
                 init() {
                     this.scrollContainer = this.$refs.categoryScroll;
                     this.checkScroll();
                     
                     // Check scroll on resize
                     window.addEventListener('resize', () => this.checkScroll());
                     this.scrollContainer.addEventListener('scroll', () => this.checkScroll());
                 },
                 
                 checkScroll() {
                     if (!this.scrollContainer) return;
                     
                     this.canScrollLeft = this.scrollContainer.scrollLeft > 0;
                     this.canScrollRight = 
                         this.scrollContainer.scrollLeft < 
                         (this.scrollContainer.scrollWidth - this.scrollContainer.clientWidth - 1);
                 },
                 
                 scrollLeft() {
                     if (!this.scrollContainer) return;
                     const scrollAmount = this.scrollContainer.clientWidth * 0.8;
                     this.scrollContainer.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
                 },
                 
                 scrollRight() {
                     if (!this.scrollContainer) return;
                     const scrollAmount = this.scrollContainer.clientWidth * 0.8;
                     this.scrollContainer.scrollBy({ left: scrollAmount, behavior: 'smooth' });
                 }
             }">
            <div class="relative">
                <!-- Left Arrow -->
                <button 
                    x-show="canScrollLeft"
                    x-transition
                    @click="scrollLeft()"
                    class="absolute left-0 top-0 bottom-0 z-10 px-3 bg-white/90 hover:bg-cream-50 flex items-center justify-center border-r border-cream-200 transition-colors"
                    aria-label="Scroll left"
                >
                    <svg class="w-5 h-5 text-warm" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

                <!-- Categories Container -->
                <div 
                    x-ref="categoryScroll"
                    class="overflow-x-auto scrollbar-hide scroll-smooth"
                    style="scrollbar-width: none; -ms-overflow-style: none;"
                >
                    <div class="flex gap-4 py-4 px-4">
                        <!-- All Categories Button -->
                        <button
                            wire:click="clearFilter"
                            class="flex-shrink-0 px-4 py-2 rounded-full font-medium text-sm transition-all duration-200 whitespace-nowrap {{ $categoryFilter === null ? 'bg-warm text-cream shadow-cozy' : 'bg-cream-100 text-warm-darker hover:bg-cream-200 hover:text-warm-dark' }}"
                        >
                            All Products
                        </button>
                        
                        @foreach($categories as $category)
                            <button
                                wire:click="filterByCategory('{{ $category->slug }}')"
                                class="flex-shrink-0 px-4 py-2 rounded-full font-medium text-sm transition-all duration-200 whitespace-nowrap {{ $categoryFilter === $category->slug ? 'bg-warm text-cream shadow-cozy' : 'bg-cream-100 text-warm-darker hover:bg-cream-200 hover:text-warm-dark' }}"
                            >
                                {{ $category->name }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Right Arrow -->
                <button 
                    x-show="canScrollRight"
                    x-transition
                    @click="scrollRight()"
                    class="absolute right-0 top-0 bottom-0 z-10 px-3 bg-white/90 hover:bg-cream-50 flex items-center justify-center border-l border-cream-200 transition-colors"
                    aria-label="Scroll right"
                >
                    <svg class="w-5 h-5 text-warm" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </div>
    @endif

    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6 animate-fade-in">
            <h1 class="text-2xl font-semibold text-warm-darker">
                @if($selectedCategory)
                    {{ $selectedCategory->name }}
                @else
                    Products
                @endif
            </h1>
            @if($selectedCategory)
                <button
                    wire:click="clearFilter"
                    class="text-sm text-warm hover:text-warm-dark font-medium transition-colors"
                >
                    Clear Filter
                </button>
            @endif
        </div>
        
        @if($selectedCategory)
            <p class="text-warm/80 mb-6 animate-fade-in">
                Showing {{ $products->total() }} {{ $products->total() === 1 ? 'product' : 'products' }} in {{ $selectedCategory->name }}
            </p>
        @endif

        @if ($products->count())
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach ($products as $index => $product)
                    @php
                        $qty = $cartQuantities[$product->id] ?? 0;
                    @endphp

                    <div class="card-cozy overflow-hidden flex flex-col relative group animate-fade-in-up" style="animation-delay: {{ min($index * 50, 300) }}ms;">
                        <div class="aspect-[4/3] bg-cream-100 relative overflow-hidden"
                             x-data="{
                                 currentImage: 0,
                                 images: @js($product->images->pluck('path')->toArray()),
                                 get hasMultipleImages() {
                                     return this.images.length > 1;
                                 },
                                 nextImage() {
                                     if (this.hasMultipleImages) {
                                         this.currentImage = (this.currentImage + 1) % this.images.length;
                                     }
                                 },
                                 prevImage() {
                                     if (this.hasMultipleImages) {
                                         this.currentImage = (this.currentImage - 1 + this.images.length) % this.images.length;
                                     }
                                 },
                                 goToImage(index) {
                                     this.currentImage = index;
                                 }
                             }">
                            <a href="{{ route('products.show', $product->slug) }}" class="block w-full h-full">
                                @if($product->images->count() > 0)
                                    <!-- Image Carousel -->
                                    <div class="relative w-full h-full">
                                        @foreach($product->images as $index => $image)
                                            <img 
                                                x-show="currentImage === {{ $index }}"
                                                x-transition:enter="transition ease-out duration-300"
                                                x-transition:enter-start="opacity-0"
                                                x-transition:enter-end="opacity-100"
                                                x-transition:leave="transition ease-in duration-200"
                                                x-transition:leave-start="opacity-100"
                                                x-transition:leave-end="opacity-0"
                                                src="{{ $image->url }}"
                                                alt="{{ $product->name }} - Image {{ $index + 1 }}"
                                                class="absolute inset-0 w-full h-full object-cover"
                                                loading="lazy"
                                                onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'400\' height=\'300\'%3E%3Crect fill=\'%23f3f4f6\' width=\'400\' height=\'300\'/%3E%3Ctext fill=\'%239ca3af\' font-family=\'sans-serif\' font-size=\'14\' x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dominant-baseline=\'middle\'%3ENo Image%3C/text%3E%3C/svg%3E'"
                                            />
                                        @endforeach
                                    </div>
                                @else
                                    <!-- Placeholder when no images -->
                                    <div class="w-full h-full flex items-center justify-center text-gray-400 text-sm">
                                        No Image
                                    </div>
                                @endif
                            </a>

                            <!-- Navigation Arrows (only show if multiple images) - Outside the link -->
                            <template x-if="hasMultipleImages">
                                <div>
                                    <!-- Previous Button -->
                                    <button 
                                        type="button"
                                        @click="prevImage()"
                                        class="absolute left-2 top-1/2 -translate-y-1/2 bg-black bg-opacity-50 hover:bg-opacity-70 text-white p-1.5 rounded-full transition opacity-0 group-hover:opacity-100 z-20"
                                        aria-label="Previous image"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                                        </svg>
                                    </button>

                                    <!-- Next Button -->
                                    <button 
                                        type="button"
                                        @click="nextImage()"
                                        class="absolute right-2 top-1/2 -translate-y-1/2 bg-black bg-opacity-50 hover:bg-opacity-70 text-white p-1.5 rounded-full transition opacity-0 group-hover:opacity-100 z-20"
                                        aria-label="Next image"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                </div>
                            </template>

                            <!-- Image Indicators (dots) - Outside the link -->
                            <template x-if="hasMultipleImages">
                                <div class="absolute bottom-2 left-1/2 -translate-x-1/2 flex gap-1.5 z-20">
                                    <template x-for="(image, index) in images" :key="index">
                                        <button 
                                            type="button"
                                            @click="goToImage(index)"
                                            :class="currentImage === index ? 'bg-white w-2.5' : 'bg-white bg-opacity-50 w-2'"
                                            class="h-2 rounded-full transition-all duration-200 hover:bg-opacity-75"
                                            :aria-label="'Go to image ' + (index + 1)"
                                        ></button>
                                    </template>
                                </div>
                            </template>
                        </div>

                        <a href="{{ route('products.show', $product->slug) }}" class="block group/link">
                            <div class="p-4 space-y-1">
                                <div class="text-sm text-warm/70">
                                    {{ $product->category?->name ?? 'Uncategorized' }}
                                </div>
                                <div class="font-medium text-warm-darker group-hover/link:text-warm-dark transition-colors">
                                    {{ $product->name }}
                                </div>
                                <div class="text-warm-dark font-semibold">
                                    ${{ number_format($product->price, 2) }}
                                </div>
                            </div>
                        </a>

                        <!-- Action Icons -->
                        <div class="px-4 pb-4 mt-auto flex items-center justify-between gap-2">
                            @if ($qty > 0)
                                <div class="flex items-center border border-cream-300 rounded-lg overflow-hidden">
                                    <button
                                        wire:click="decrementProduct({{ $product->id }})"
                                        wire:loading.attr="disabled"
                                        wire:loading.class="opacity-50 cursor-wait"
                                        class="px-2 py-1 text-warm hover:bg-cream-50 transition"
                                    >
                                        -
                                    </button>
                                    <div class="px-3 py-1 border-x border-cream-300 text-sm text-warm-darker">
                                        {{ $qty }}
                                    </div>
                                    <button
                                        wire:click="incrementProduct({{ $product->id }})"
                                        wire:loading.attr="disabled"
                                        wire:loading.class="opacity-50 cursor-wait"
                                        class="px-2 py-1 text-warm hover:bg-cream-50 transition"
                                    >
                                        +
                                    </button>
                                </div>
                            @else
                                <div></div>
                            @endif

                            <div class="flex items-center gap-2 ml-auto">
                                @if ($qty == 0)
                                    <button
                                        wire:click="addToCart({{ $product->id }})"
                                        wire:loading.attr="disabled"
                                        wire:loading.class="opacity-60 cursor-wait"
                                        class="flex items-center justify-center p-2 border border-warm text-warm rounded-lg hover:bg-cream-100 transition"
                                        title="Add to Cart"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l3-8H6.4M7 13L5.4 5M7 13l-2 5m5 5a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm7 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2z" />
                                        </svg>
                                        <span class="text-xs font-medium ml-0.5">+</span>
                                    </button>
                                @endif

                                @if(in_array($product->id, $wishlistProductIds))
                                    <button
                                        wire:click="addToWishlist({{ $product->id }})"
                                        wire:loading.attr="disabled"
                                        wire:loading.class="opacity-60 cursor-wait"
                                        class="flex items-center justify-center p-2 border border-red-400 text-red-500 rounded-lg hover:bg-red-50 transition"
                                        title="Remove from Wishlist"
                                    >
                                @else
                                    <button
                                        wire:click="addToWishlist({{ $product->id }})"
                                        wire:loading.attr="disabled"
                                        wire:loading.class="opacity-60 cursor-wait"
                                        class="flex items-center justify-center p-2 border border-cream-300 text-warm/70 rounded-lg hover:border-red-400 hover:text-red-500 hover:bg-red-50/50 transition"
                                        title="Add to Wishlist"
                                    >
                                @endif
                                    @if(in_array($product->id, $wishlistProductIds))
                                        <!-- Filled Heart -->
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                        </svg>
                                    @else
                                        <!-- Outline Heart -->
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 0 1 6.364 0L12 7.636l1.318-1.318a4.5 4.5 0 1 1 6.364 6.364L12 21.364l-7.682-7.682a4.5 4.5 0 0 1 0-6.364z" />
                                        </svg>
                                        <span class="text-xs font-medium ml-0.5">+</span>
                                    @endif
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $products->links() }}
            </div>
        @else
            <x-empty-state
                title="No products found"
                :description="$selectedCategory ? 'Try another category or clear the filter.' : 'Check back later for new items.'"
                :ctaText="$selectedCategory ? null : null"
                :ctaUrl="null"
            >
                <x-slot:icon>
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8 4-8-4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </x-slot:icon>
                @if($selectedCategory)
                    <div class="mt-6">
                        <button wire:click="clearFilter"
                                class="btn-cozy">
                            Clear filter
                        </button>
                    </div>
                @endif
            </x-empty-state>
        @endif
    </div>
</div>
