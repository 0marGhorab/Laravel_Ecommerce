<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;

class OrderHistoryPage extends Component
{
    use WithPagination;

    public function mount()
    {
        // Redirect if not authenticated
        if (!auth()->check()) {
            redirect()->route('login');
        }
    }

    public function render()
    {
        $orders = Order::where('user_id', auth()->id())
            ->with(['items.product.images', 'shippingAddress', 'billingAddress'])
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('livewire.order-history-page', [
            'orders' => $orders,
        ]);
    }
}
