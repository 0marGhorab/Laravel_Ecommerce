@extends('layouts.admin')

@section('title', 'Products')
@section('heading', 'Products')

@section('content')
    <div class="bg-white rounded-lg shadow">
        <div class="p-4 border-b border-gray-200 flex flex-wrap items-center justify-between gap-4">
            <form method="GET" action="{{ route('admin.products.index') }}" class="flex flex-wrap gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or SKU..." class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                <select name="status" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <option value="">All statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
                <button type="submit" class="px-3 py-1.5 bg-gray-800 text-white text-sm rounded-md hover:bg-gray-700">Filter</button>
            </form>
            <a href="{{ route('admin.products.create') }}" class="px-3 py-1.5 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">Add product</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Updated</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($products as $product)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $product->sku ?? '—' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $product->category?->name ?? '—' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($product->price, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $product->stock_quantity ?? 0 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    {{ $product->status === 'active' ? 'bg-green-100 text-green-800' : ($product->status === 'draft' ? 'bg-gray-100 text-gray-800' : 'bg-amber-100 text-amber-800') }}">{{ $product->status }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $product->updated_at->format('M j, Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <a href="{{ route('admin.products.edit', $product) }}" class="text-indigo-600 hover:text-indigo-800 mr-3">Edit</a>
                                <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="inline" onsubmit="return confirm('Delete this product?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500">No products found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($products->hasPages())
            <div class="px-6 py-3 border-t border-gray-200">
                {{ $products->links() }}
            </div>
        @endif
    </div>
@endsection
