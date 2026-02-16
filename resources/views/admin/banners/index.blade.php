@extends('layouts.admin')

@section('title', 'Banners')
@section('heading', 'Promo Banners')

@section('content')
    <div class="bg-white rounded-lg shadow">
        <div class="p-4 border-b border-gray-200 flex flex-wrap items-center justify-between gap-4">
            <p class="text-sm text-gray-600">Banners appear as a slideshow at the top of the product detail page.</p>
            <a href="{{ route('admin.banners.create') }}" class="px-3 py-1.5 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">Add banner</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Preview</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Active</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($banners as $banner)
                        <tr>
                            <td class="px-6 py-4">
                                @if($banner->image_url)
                                    <div class="w-24 h-14 rounded overflow-hidden bg-gray-100">
                                        <img src="{{ $banner->image_url }}" alt="" class="w-full h-full object-cover">
                                    </div>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $banner->title ?: '—' }}</div>
                                @if($banner->subtitle)
                                    <div class="text-xs text-gray-500">{{ Str::limit($banner->subtitle, 40) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $banner->sort_order }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($banner->is_active)
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Yes</span>
                                @else
                                    <span class="text-gray-400">No</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <a href="{{ route('admin.banners.edit', $banner) }}" class="text-indigo-600 hover:text-indigo-800 mr-3">Edit</a>
                                <form method="POST" action="{{ route('admin.banners.destroy', $banner) }}" class="inline" onsubmit="return confirm('Delete this banner?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">No banners yet. Add one to show a slideshow on the product page.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($banners->hasPages())
            <div class="px-6 py-3 border-t border-gray-200">
                {{ $banners->links() }}
            </div>
        @endif
    </div>
@endsection
