<?php

namespace App\Livewire;

use App\Models\Wishlist;
use App\Models\Cart;
use Livewire\Component;

class WishlistPage extends Component
{
    public ?Wishlist $wishlist = null;

    public function mount()
    {
        if (auth()->check()) {
            $this->wishlist = Wishlist::firstOrCreate(
                ['user_id' => auth()->id(), 'name' => 'Default']
            );
        }
    }

    public function removeItem(int $itemId): void
    {
        if (!auth()->check() || !$this->wishlist) {
            return;
        }

        $this->wishlist->items()->whereKey($itemId)->delete();
        $this->wishlist->refresh()->load('items.product');
        $wishlistCount = $this->wishlist->items()->count();
        $this->dispatch('wishlist-updated', count: $wishlistCount);
    }

    public function addToCart(int $productId): void
    {
        $product = \App\Models\Product::findOrFail($productId);

        $cart = Cart::current();

        $item = $cart->items()->firstOrNew(['product_id' => $product->id]);
        $item->quantity = $item->exists ? $item->quantity + 1 : 1;
        $item->unit_price = $product->price;
        $item->total_price = $item->quantity * $item->unit_price;
        $item->save();

        $cart->refresh();
        $cartCount = $cart->items->sum('quantity');

        $this->dispatch('product-added');
        $this->dispatch('cart-updated', count: $cartCount);
    }

    public function render()
    {
        $cartProductIds = [];

        if (auth()->check() && $this->wishlist) {
            $this->wishlist->loadMissing('items.product');

            // Get cart product IDs to check which wishlist items are already in cart
            $cart = Cart::current();
            $cartProductIds = $cart->items()
                ->pluck('product_id')
                ->toArray();
        }

        return view('livewire.wishlist-page', [
            'cartProductIds' => $cartProductIds,
        ]);
    }
}