<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            <livewire:layout.navigation />

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>

            <!-- Global toast for Livewire events -->
            <div
                x-data="{ 
                    show: false, 
                    message: '', 
                    type: 'success',
                    init() {
                        // Check for login success flash message
                        @if(session('login_success'))
                            this.message = 'Logged in successfully';
                            this.type = 'success';
                            this.show = true;
                            setTimeout(() => this.show = false, 3000);
                        @endif
                    }
                }"
                x-on:product-added.window="
                    message = 'Product added to cart successfully';
                    type = 'success';
                    show = true;
                    setTimeout(() => show = false, 2500);
                "
                x-on:wishlist-added.window="
                    message = 'Product added to wishlist';
                    type = 'success';
                    show = true;
                    setTimeout(() => show = false, 2500);
                "
                x-on:wishlist-removed.window="
                    message = 'Product removed from wishlist';
                    type = 'info';
                    show = true;
                    setTimeout(() => show = false, 2500);
                "
                x-on:wishlist-login-required.window="
                    message = 'Please login to add items to wishlist';
                    type = 'warning';
                    show = true;
                    setTimeout(() => show = false, 3000);
                "
                x-show="show"
                x-transition
                :class="{
                    'bg-green-600': type === 'success',
                    'bg-blue-600': type === 'info',
                    'bg-yellow-600': type === 'warning'
                }"
                class="fixed bottom-4 right-4 z-50 text-white px-4 py-3 rounded shadow-lg text-sm flex items-center gap-3"
                style="display: none;"
            >
                <span x-text="message"></span>

                <template x-if="type === 'success' && message.includes('cart')">
                    <a
                        href="{{ route('cart.index') }}"
                        class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-white text-green-700 text-xs font-semibold rounded hover:bg-gray-100 transition"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M3 3h2l.4 2M7 13h10l3-8H6.4M7 13L5.4 5M7 13l-2 5m5-5v5m4-5v5" />
                        </svg>
                        <span>Go to Cart</span>
                    </a>
                </template>

                <template x-if="type === 'success' && message.includes('wishlist')">
                    <a
                        href="{{ route('wishlist.index') }}"
                        class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-white text-red-700 text-xs font-semibold rounded hover:bg-gray-100 transition"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M4.318 6.318a4.5 4.5 0 0 1 6.364 0L12 7.636l1.318-1.318a4.5 4.5 0 1 1 6.364 6.364L12 21.364l-7.682-7.682a4.5 4.5 0 0 1 0-6.364z" />
                        </svg>
                        <span>View Wishlist</span>
                    </a>
                </template>

                <template x-if="type === 'warning' && message.includes('login')">
                    <a
                        href="{{ route('login') }}"
                        class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-white text-yellow-700 text-xs font-semibold rounded hover:bg-gray-100 transition"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        <span>Login</span>
                    </a>
                </template>
            </div>

            <!-- Order Success Modal -->
            @if(session('order_placed_success'))
                <div
                    x-data="{ 
                        show: true,
                        init() {
                            // Auto-close after 2 seconds
                            setTimeout(() => {
                                this.show = false;
                            }, 2000);
                        }
                    }"
                    x-show="show"
                    x-transition
                    class="fixed inset-0 z-50 overflow-y-auto"
                >
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <!-- Background overlay -->
                    <div
                        x-show="show"
                        x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"
                        @click="show = false"
                    ></div>

                    <!-- Modal panel -->
                    <div
                        x-show="show"
                        x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
                    >
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                                        Order Placed Successfully!
                                    </h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500">
                                            Your order has been placed successfully. You will receive a confirmation email shortly.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button
                                @click="show = false"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm"
                            >
                                Continue Shopping
                            </button>
                        </div>
                    </div>
                </div>
                </div>
            @endif
        </div>

        @livewireScripts
    </body>
</html>

