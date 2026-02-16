@extends('layouts.admin')

@section('title', 'Orders')
@section('heading', 'Orders')

@section('content')
    <div class="bg-white rounded-lg shadow">
        <div class="p-4 border-b border-gray-200 sm:flex sm:items-center sm:justify-between gap-4">
            <form method="GET" action="{{ route('admin.orders.index') }}" class="flex flex-wrap gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Order # or customer..." class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                <select name="status" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <option value="">All statuses</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>Shipped</option>
                    <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                <button type="submit" class="px-3 py-1.5 bg-gray-800 text-white text-sm rounded-md hover:bg-gray-700">Filter</button>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($orders as $order)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $order->order_number }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $order->user?->name ?? '—' }}<br><span class="text-gray-400">{{ $order->user?->email }}</span></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    @if($order->status === 'delivered') bg-green-100 text-green-800
                                    @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                    @else bg-amber-100 text-amber-800
                                    @endif">{{ $order->status }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $order->payment_status ?? '—' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($order->grand_total, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->created_at->format('M j, Y H:i') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <a href="{{ route('admin.orders.show', $order) }}" class="text-indigo-600 hover:text-indigo-800 mr-3">View</a>
                                <form method="POST" action="{{ route('admin.orders.destroy', $order) }}" class="inline" onsubmit="return confirm('Delete this order?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">No orders found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
            <div class="px-6 py-3 border-t border-gray-200">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
@endsection
