@extends('layouts.admin')

@section('title', 'Dashboard')
@section('heading', 'Dashboard')

@section('content')
    <div class="space-y-6">
        <!-- Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm font-medium text-gray-500">Total Orders</div>
                <div class="mt-1 text-2xl font-semibold text-gray-900">{{ number_format($ordersCount) }}</div>
                <a href="{{ route('admin.orders.index') }}" class="mt-2 text-sm text-indigo-600 hover:text-indigo-800">View all</a>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm font-medium text-gray-500">Products</div>
                <div class="mt-1 text-2xl font-semibold text-gray-900">{{ number_format($productsCount) }}</div>
                <a href="{{ route('admin.products.index') }}" class="mt-2 text-sm text-indigo-600 hover:text-indigo-800">View all</a>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm font-medium text-gray-500">Customers</div>
                <div class="mt-1 text-2xl font-semibold text-gray-900">{{ number_format($usersCount) }}</div>
                <a href="{{ route('admin.users.index') }}" class="mt-2 text-sm text-indigo-600 hover:text-indigo-800">View all</a>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm font-medium text-gray-500">Revenue (paid)</div>
                <div class="mt-1 text-2xl font-semibold text-gray-900">${{ number_format($totalRevenue, 2) }}</div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Recent Orders</h2>
                <a href="{{ route('admin.orders.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">View all</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($recentOrders as $order)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $order->order_number }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $order->user?->name ?? '—' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        @if($order->status === 'delivered') bg-green-100 text-green-800
                                        @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                        @else bg-amber-100 text-amber-800
                                        @endif">{{ $order->status }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($order->grand_total, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->created_at->format('M j, Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="text-indigo-600 hover:text-indigo-800">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">No orders yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
