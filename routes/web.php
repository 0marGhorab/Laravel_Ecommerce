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

// Admin (auth + is_admin)
Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    Route::get('/', \App\Http\Controllers\Admin\DashboardController::class)->name('dashboard');
    Route::get('/orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [\App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::delete('/orders/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'destroy'])->name('orders.destroy');
    Route::resource('products', \App\Http\Controllers\Admin\ProductController::class)->except(['show']);
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->only(['index', 'edit', 'update', 'destroy']);
    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class)->except(['show']);
    Route::resource('banners', \App\Http\Controllers\Admin\BannerController::class)->except(['show']);
});

require __DIR__.'/auth.php';