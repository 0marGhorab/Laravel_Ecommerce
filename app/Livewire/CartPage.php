<?php

namespace App\Livewire;

use App\Models\Cart;
use Livewire\Component;

class CartPage extends Component
{
    public Cart $cart;

    public function mount(): void
    {
        $this->cart = Cart::current();
    }

    public function incrementItem(int $itemId): void
    {
        $item = $this->cart->items()->findOrFail($itemId);
        $item->quantity++;
        $item->total_price = $item->quantity * $item->unit_price;
        $item->save();

        Cart::clearCache();
        $this->cart->refresh()->load('items.product');
        $cartCount = $this->cart->items->sum('quantity');
        $this->dispatch('cart-updated', count: $cartCount);
    }

    public function decrementItem(int $itemId): void
    {
        $item = $this->cart->items()->findOrFail($itemId);

        if ($item->quantity <= 1) {
            $item->delete();
        } else {
            $item->quantity--;
            $item->total_price = $item->quantity * $item->unit_price;
            $item->save();
        }

        Cart::clearCache();
        $this->cart->refresh()->load('items.product');
        $cartCount = $this->cart->items->sum('quantity');
        $this->dispatch('cart-updated', count: $cartCount);
    }

    public function removeItem(int $itemId): void
    {
        $this->cart->items()->whereKey($itemId)->delete();
        Cart::clearCache();
        $this->cart->refresh()->load('items.product');
        $cartCount = $this->cart->items->sum('quantity');
        $this->dispatch('cart-updated', count: $cartCount);
    }

    public function getSubtotalProperty(): float
    {
        return (float) $this->cart->items->sum('total_price');
    }

    public function render()
    {
        return view('livewire.cart-page');
    }
}
