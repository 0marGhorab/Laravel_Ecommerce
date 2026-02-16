<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\OrderStatusUpdatedMail;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $query = Order::with(['user', 'items.product'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
            });
        }

        $orders = $query->paginate(15)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        $order->load(['user', 'items.product.images', 'shippingAddress', 'billingAddress']);

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'payment_status' => 'nullable|in:pending,paid,failed,refunded',
        ]);

        $previousStatus = $order->status;

        $data = ['status' => $request->status];
        if ($request->filled('payment_status')) {
            $data['payment_status'] = $request->payment_status;
        }
        $order->update($data);

        // Notify customer of status change (only when status actually changed)
        if ($previousStatus !== $order->status && $order->user && $order->user->email) {
            Mail::to($order->user->email)->send(new OrderStatusUpdatedMail($order, $previousStatus));
        }

        return back()->with('success', 'Order updated.');
    }

    public function destroy(Order $order): RedirectResponse
    {
        $order->items()->delete();
        $order->delete();
        return redirect()->route('admin.orders.index')->with('success', 'Order deleted.');
    }
}
