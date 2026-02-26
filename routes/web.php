<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\ProductIndex;
use App\Livewire\ProductShow;
use App\Livewire\CartPage;
use App\Livewire\WishlistPage;
use App\Livewire\CheckoutPage;
use App\Livewire\OrderHistoryPage;
use App\Livewire\OrderShowPage;
use App\Http\Controllers\Api\ProductController as ApiProductController;
use App\Http\Controllers\Api\CategoryController as ApiCategoryController;
use App\Http\Controllers\Api\CartController as ApiCartController;
use App\Http\Controllers\Api\WishlistController as ApiWishlistController;
use App\Http\Controllers\Api\OrderController as ApiOrderController;

Route::get('/', ProductIndex::class)->name('products.index');

Route::get('/cart', CartPage::class)->name('cart.index');

Route::get('/checkout', CheckoutPage::class)->name('checkout.index');
Route::get('/checkout/stripe/success', [\App\Http\Controllers\StripeCheckoutController::class, 'success'])->name('checkout.stripe.success')->middleware('auth');
Route::get('/checkout/stripe/cancel', [\App\Http\Controllers\StripeCheckoutController::class, 'cancel'])->name('checkout.stripe.cancel')->middleware('auth');

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
Route::get('/orders/{orderNumber}/invoice', [\App\Http\Controllers\OrderInvoiceController::class, '__invoke'])
    ->middleware(['auth'])
    ->name('orders.invoice');

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

// JSON API (session-authenticated, for internal/front-end use)
Route::prefix('api')->name('api.')->group(function () {
    // Public catalog
    Route::get('/products', [ApiProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product}', [ApiProductController::class, 'show'])->name('products.show');
    Route::get('/categories', [ApiCategoryController::class, 'index'])->name('categories.index');

    // Cart is per-session (guest or auth)
    Route::get('/cart', [ApiCartController::class, 'show'])->name('cart.show');
    Route::post('/cart/items', [ApiCartController::class, 'addItem'])->name('cart.items.store');
    Route::patch('/cart/items/{item}', [ApiCartController::class, 'updateItem'])->name('cart.items.update');
    Route::delete('/cart/items/{item}', [ApiCartController::class, 'removeItem'])->name('cart.items.destroy');
    Route::delete('/cart', [ApiCartController::class, 'clear'])->name('cart.clear');

    // Authenticated user resources
    Route::middleware('auth')->group(function () {
        Route::get('/wishlist', [ApiWishlistController::class, 'index'])->name('wishlist.index');
        Route::post('/wishlist', [ApiWishlistController::class, 'store'])->name('wishlist.store');
        Route::delete('/wishlist/{product}', [ApiWishlistController::class, 'destroy'])->name('wishlist.destroy');

        Route::get('/orders', [ApiOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{orderNumber}', [ApiOrderController::class, 'show'])->name('orders.show');
    });
});