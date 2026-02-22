<?php

namespace App\Livewire;

use App\Models\Cart;
use App\Models\Order;
use Livewire\Component;

class OrderShowPage extends Component
{
    public string $orderNumber;
    public Order $order;

    public function mount($orderNumber = null): void
    {
        // Route param may not be injected in some setups; resolve from request
        $this->orderNumber = $orderNumber ?? request()->route('orderNumber');

        if (!$this->orderNumber || !is_string($this->orderNumber)) {
            $this->redirect(route('orders.index'), navigate: true);
            return;
        }

        if (!auth()->check()) {
            $this->redirect(route('login'), navigate: true);
            return;
        }

        $this->order = Order::where('order_number', $this->orderNumber)
            ->where('user_id', auth()->id())
            ->with(['items.product.images', 'shippingAddress', 'billingAddress'])
            ->first();

        if (!$this->order) {
            session()->flash('error', 'Order not found.');
            $this->redirect(route('orders.index'), navigate: true);
            return;
        }
    }

    public function reorder(): void
    {
        $cart = Cart::current();
        $added = 0;
        foreach ($this->order->items as $orderItem) {
            $product = $orderItem->product;
            if (!$product) {
                continue;
            }
            $item = $cart->items()->firstOrNew(['product_id' => $product->id]);
            $item->quantity = $item->exists ? $item->quantity + $orderItem->quantity : $orderItem->quantity;
            $item->unit_price = $product->price;
            $item->total_price = $item->quantity * $item->unit_price;
            $item->save();
            $added++;
        }
        Cart::clearCache();
        if ($added > 0) {
            $this->redirect(route('cart.index'), navigate: true);
        }
    }

    public function render()
    {
        return view('livewire.order-show-page', [
            'order' => $this->order,
        ]);
    }
}
