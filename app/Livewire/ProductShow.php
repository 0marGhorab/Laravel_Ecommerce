<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Wishlist;
use App\Models\Cart;
use Livewire\Component;

class ProductShow extends Component
{
    public string $slug;
    public Product $product;

    public function mount(string $slug): void
    {
        $this->slug = $slug;
        $this->product = Product::with(['category', 'images'])
            ->where('slug', $slug)
            ->firstOrFail();
    }

    public function addToCart(): void
    {
        $product = $this->product;

        $cart = \App\Models\Cart::current();

        $item = $cart->items()->firstOrNew(['product_id' => $product->id]);
        $item->quantity = $item->exists ? $item->quantity + 1 : 1;
        $item->unit_price = $product->price;
        $item->total_price = $item->quantity * $item->unit_price;
        $item->save();

        Cart::clearCache();
        $cart->refresh()->load('items.product');
        $cartCount = $cart->items->sum('quantity');

        $this->dispatch('product-added');
        $this->dispatch('cart-updated', count: $cartCount);
    }

    public function incrementProduct(): void
    {
        $this->addToCart();
    }

    public function decrementProduct(): void
    {
        $cart = Cart::current();

        $item = $cart->items()->where('product_id', $this->product->id)->first();

        if (! $item) {
            return;
        }

        if ($item->quantity <= 1) {
            $item->delete();
        } else {
            $item->quantity--;
            $item->total_price = $item->quantity * $item->unit_price;
            $item->save();
        }

        Cart::clearCache();
        $cart->refresh()->load('items.product');
        $cartCount = $cart->items->sum('quantity');
        $this->dispatch('cart-updated', count: $cartCount);
    }

    public function addToWishlist(): void
    {
        if (! auth()->check()) {
            $this->dispatch('wishlist-login-required');
            return;
        }

        $wishlist = Wishlist::with('items')
            ->firstOrCreate(
                ['user_id' => auth()->id(), 'name' => 'Default']
            );

        $exists = $wishlist->items->contains('product_id', $this->product->id);

        if ($exists) {
            $wishlist->items()->where('product_id', $this->product->id)->delete();
            $this->dispatch('wishlist-removed');
        } else {
            $wishlist->items()->create(['product_id' => $this->product->id]);
            $this->dispatch('wishlist-added');
        }

        $wishlistCount = $wishlist->items()->count();
        $this->dispatch('wishlist-updated', count: $wishlistCount);
    }

    public function render()
    {
        // Optimize wishlist check with eager loading
        $isInWishlist = false;
        if (auth()->check()) {
            $wishlist = Wishlist::with('items')
                ->where('user_id', auth()->id())
                ->where('name', 'Default')
                ->first();
            
            if ($wishlist) {
                $isInWishlist = $wishlist->items->contains('product_id', $this->product->id);
            }
        }

        // Get cart quantity for this product
        $cart = Cart::current();
        $cartItem = $cart->items()->where('product_id', $this->product->id)->first();
        $cartQuantity = $cartItem ? $cartItem->quantity : 0;

        return view('livewire.product-show', [
            'product' => $this->product,
            'isInWishlist' => $isInWishlist,
            'cartQuantity' => $cartQuantity,
        ]);
    }
}
