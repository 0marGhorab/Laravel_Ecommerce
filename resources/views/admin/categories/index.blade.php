@extends('layouts.admin')

@section('title', 'Categories')
@section('heading', 'Categories')

@section('content')
    <div class="bg-white rounded-lg shadow">
        <div class="p-4 border-b border-gray-200 flex flex-wrap items-center justify-between gap-4">
            <form method="GET" action="{{ route('admin.categories.index') }}" class="flex flex-wrap gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or slug..." class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                <button type="submit" class="px-3 py-1.5 bg-gray-800 text-white text-sm rounded-md hover:bg-gray-700">Search</button>
            </form>
            <a href="{{ route('admin.categories.create') }}" class="px-3 py-1.5 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">Add category</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Slug</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Products</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Active</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($categories as $category)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $category->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $category->slug }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $category->products_count }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($category->is_active)
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Yes</span>
                                @else
                                    <span class="text-gray-400">No</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <a href="{{ route('admin.categories.edit', $category) }}" class="text-indigo-600 hover:text-indigo-800 mr-3">Edit</a>
                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="inline" onsubmit="return confirm('Delete this category?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">No categories found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($categories->hasPages())
            <div class="px-6 py-3 border-t border-gray-200">
                {{ $categories->links() }}
            </div>
        @endif
    </div>
@endsection
