<?php

use App\Livewire\Actions\Logout;
use App\Models\Cart;
use App\Models\Wishlist;
use App\Models\Product;
use Livewire\Volt\Component;

new class extends Component
{
    public string $search = '';

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }

    public function getSearchResultsProperty()
    {
        if (strlen($this->search) < 2) {
            return collect();
        }

        return Product::with(['category', 'images' => function($query) {
            $query->orderBy('is_primary', 'desc')->orderBy('sort_order')->limit(1);
        }])
            ->where('status', 'active')
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('short_description', 'like', '%' . $this->search . '%')
                    ->orWhere('long_description', 'like', '%' . $this->search . '%')
                    ->orWhere('sku', 'like', '%' . $this->search . '%');
            })
            ->limit(5)
            ->get();
    }
}; ?>

@php
    // Cart item count (sum of quantities)
    $cartCount = 0;
    if (class_exists(Cart::class)) {
        $cart = Cart::current();
        $cartCount = $cart->items->sum('quantity');
    }

    // Wishlist item count (for authenticated users)
    $wishlistCount = 0;
    if (auth()->check() && class_exists(Wishlist::class)) {
        $wishlist = Wishlist::firstOrCreate(
            ['user_id' => auth()->id(), 'name' => 'Default']
        );
        $wishlistCount = $wishlist->items()->count();
    }
@endphp

<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Left: Logo -->
            <div class="flex items-center">
                <a href="{{ route('products.index') }}" wire:navigate class="shrink-0 flex items-center">
                    <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                </a>
            </div>

            <!-- Center: Search bar -->
            <div class="flex-1 max-w-xl mx-4 hidden sm:block relative"
                 x-data="{ showResults: false }"
                 x-on:click.outside="showResults = false">
                <label class="relative block">
                    <span class="sr-only">Search</span>
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15z" />
                        </svg>
                    </span>
                    <input
                        type="search"
                        wire:model.live.debounce.300ms="search"
                        x-on:focus="showResults = true"
                        x-on:keydown.escape="showResults = false"
                        class="block w-full rounded-full border-gray-300 pl-9 pr-3 py-1.5 text-sm placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Search products..."
                    />
                </label>

                <!-- Search Results Dropdown -->
                <div x-show="showResults && $wire.search.length >= 2"
                     x-transition
                     class="absolute top-full left-0 right-0 mt-2 bg-white border border-gray-200 rounded-lg shadow-lg z-50 max-h-96 overflow-y-auto"
                     style="display: none;">
                    @if(strlen($this->search) >= 2)
                        @if($this->searchResults->count() > 0)
                            <div class="py-2">
                                @foreach($this->searchResults as $product)
                                    <a href="{{ route('products.show', $product->slug) }}"
                                       wire:navigate
                                       x-on:click="showResults = false"
                                       class="flex items-center gap-3 px-4 py-2 hover:bg-gray-50 transition">
                                        <div class="w-12 h-12 bg-gray-100 rounded overflow-hidden flex-shrink-0">
                                            @if($product->images->count() > 0)
                                                <img 
                                                    src="{{ $product->images->first()->url }}"
                                                    alt="{{ $product->name }}"
                                                    class="w-full h-full object-cover"
                                                    loading="lazy"
                                                    onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'48\' height=\'48\'%3E%3Crect fill=\'%23f3f4f6\' width=\'48\' height=\'48\'/%3E%3Ctext fill=\'%239ca3af\' font-family=\'sans-serif\' font-size=\'10\' x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dominant-baseline=\'middle\'%3ENo Image%3C/text%3E%3C/svg%3E'"
                                                />
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-xs text-gray-400">
                                                    No Image
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="font-medium text-gray-900 truncate">
                                                {{ $product->name }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $product->category?->name ?? 'Uncategorized' }}
                                            </div>
                                            <div class="text-sm font-semibold text-indigo-600">
                                                ${{ number_format($product->price, 2) }}
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <div class="px-4 py-6 text-center text-gray-500 text-sm">
                                No products found for "{{ $this->search }}"
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Right: Wishlist, Cart, User -->
            <div class="flex items-center gap-4">
                <!-- Wishlist -->
                <a href="{{ route('wishlist.index') }}" 
                   x-data="{ count: {{ $wishlistCount }} }"
                   x-on:wishlist-updated.window="count = $event.detail.count"
                   class="relative text-gray-600 hover:text-red-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M4.318 6.318a4.5 4.5 0 0 1 6.364 0L12 7.636l1.318-1.318a4.5 4.5 0 1 1 6.364 6.364L12 21.364l-7.682-7.682a4.5 4.5 0 0 1 0-6.364z" />
                    </svg>
                    <span x-show="count > 0"
                          x-transition
                          class="absolute -top-1 -right-1 inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-semibold leading-none text-white bg-red-600 rounded-full"
                          x-text="count">
                    </span>
                </a>

                <!-- Cart -->
                <a href="{{ route('cart.index') }}" 
                   x-data="{ count: {{ $cartCount }} }"
                   x-on:cart-updated.window="count = $event.detail.count"
                   class="relative text-gray-600 hover:text-indigo-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3 3h2l.4 2M7 13h10l3-8H6.4M7 13L5.4 5M7 13l-2 5m5 5a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm7 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2z" />
                    </svg>
                    <span x-show="count > 0"
                          x-transition
                          class="absolute -top-1 -right-1 inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-semibold leading-none text-white bg-indigo-600 rounded-full"
                          x-text="count">
                    </span>
                </a>

                <!-- Authentication Buttons / User Dropdown -->
                @auth
                    <div class="hidden sm:flex sm:items-center">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                    <div x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>

                                    <div class="ms-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('orders.index')" wire:navigate>
                                    {{ __('My Orders') }}
                                </x-dropdown-link>

                                <x-dropdown-link :href="route('profile')" wire:navigate>
                                    {{ __('Profile') }}
                                </x-dropdown-link>

                                <!-- Authentication -->
                                <button wire:click="logout" class="w-full text-start">
                                    <x-dropdown-link>
                                        {{ __('Log Out') }}
                                    </x-dropdown-link>
                                </button>
                            </x-slot>
                        </x-dropdown>
                    </div>
                @else
                    <!-- Sign In / Sign Up Buttons for Guests -->
                    <div class="hidden sm:flex sm:items-center gap-3">
                        <a 
                            href="{{ route('login') }}"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition"
                        >
                            Sign In
                        </a>
                        @if (Route::has('register'))
                            <a 
                                href="{{ route('register') }}"
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition"
                            >
                                Sign Up
                            </a>
                        @endif
                    </div>
                @endauth

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        @auth
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800" x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>
                    <div class="font-medium text-sm text-gray-500">{{ auth()->user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('orders.index')" wire:navigate>
                        {{ __('My Orders') }}
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('profile')" wire:navigate>
                        {{ __('Profile') }}
                    </x-responsive-nav-link>

                    <!-- Authentication -->
                    <button wire:click="logout" class="w-full text-start">
                        <x-responsive-nav-link>
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </button>
                </div>
            </div>
        @else
            <!-- Guest Authentication Links -->
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="px-4 space-y-2">
                    <a 
                        href="{{ route('login') }}"
                        class="block w-full text-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition"
                    >
                        Sign In
                    </a>
                    @if (Route::has('register'))
                        <a 
                            href="{{ route('register') }}"
                            class="block w-full text-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 transition"
                        >
                            Sign Up
                        </a>
                    @endif
                </div>
            </div>
        @endauth
    </div>
</nav>
