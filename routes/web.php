<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\ProductIndex;
use App\Livewire\ProductShow;
use App\Livewire\CartPage;
use App\Livewire\WishlistPage;
use App\Livewire\CheckoutPage;
use App\Livewire\OrderHistoryPage;
use App\Livewire\OrderShowPage;

Route::get('/', ProductIndex::class)->name('products.index');

Route::get('/cart', CartPage::class)->name('cart.index');

Route::get('/checkout', CheckoutPage::class)->name('checkout.index');

Route::get('/wishlist', WishlistPage::class)
    ->name('wishlist.index');

Route::get('/products/{slug}', ProductShow::class)
    ->name('products.show');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('/orders', OrderHistoryPage::class)
    ->middleware(['auth'])
    ->name('orders.index');

Route::get('/orders/{orderNumber}', OrderShowPage::class)
    ->middleware(['auth'])
    ->name('orders.show');

require __DIR__.'/auth.php';