<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-warm-darker leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="card-cozy overflow-hidden">
                <div class="p-8 text-warm-darker">
                    <div class="text-center py-8">
                        <h3 class="text-lg font-medium text-warm-darker mb-4">Welcome to your dashboard!</h3>
                        <div class="flex flex-col sm:flex-row gap-4 justify-center">
                            <a href="{{ route('products.index') }}" class="btn-cozy" wire:navigate>
                                Browse Products
                            </a>
                            <a href="{{ route('orders.index') }}" class="btn-cozy-soft" wire:navigate>
                                View Orders
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
