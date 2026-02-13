<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Component;

class OrderShowPage extends Component
{
    public string $orderNumber;
    public Order $order;

    public function mount(string $orderNumber): void
    {
        $this->orderNumber = $orderNumber;
        
        // Only allow users to view their own orders
        if (!auth()->check()) {
            redirect()->route('login');
            return;
        }

        $this->order = Order::where('order_number', $orderNumber)
            ->where('user_id', auth()->id())
            ->with(['items.product.images', 'shippingAddress', 'billingAddress'])
            ->firstOrFail();
    }

    public function render()
    {
        return view('livewire.order-show-page', [
            'order' => $this->order,
        ]);
    }
}
