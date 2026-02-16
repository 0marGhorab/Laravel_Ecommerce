@extends('layouts.admin')

@section('title', 'Edit banner')
@section('heading', 'Edit banner')

@section('content')
    <div class="max-w-2xl">
        <a href="{{ route('admin.banners.index') }}" class="text-sm text-indigo-600 hover:underline mb-4 inline-block">&larr; Back to banners</a>
        <form method="POST" action="{{ route('admin.banners.update', $banner) }}" enctype="multipart/form-data" class="bg-white rounded-lg shadow p-6 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label for="image" class="block text-sm font-medium text-gray-700">Background image</label>
                @if($banner->image_url)
                    <div class="mt-1 mb-2 w-full max-w-md h-32 rounded overflow-hidden bg-gray-100">
                        <img src="{{ $banner->image_url }}" alt="" class="w-full h-full object-cover">
                    </div>
                    <p class="text-xs text-gray-500 mb-1">Leave empty to keep current image.</p>
                @endif
                <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-indigo-50 file:text-indigo-700">
                @error('image')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                <input type="text" name="title" id="title" value="{{ old('title', $banner->title) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label for="subtitle" class="block text-sm font-medium text-gray-700">Subtitle</label>
                <input type="text" name="subtitle" id="subtitle" value="{{ old('subtitle', $banner->subtitle) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="cta_text" class="block text-sm font-medium text-gray-700">Button text</label>
                    <input type="text" name="cta_text" id="cta_text" value="{{ old('cta_text', $banner->cta_text) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="cta_url" class="block text-sm font-medium text-gray-700">Button URL</label>
                    <input type="text" name="cta_url" id="cta_url" value="{{ old('cta_url', $banner->cta_url) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="sort_order" class="block text-sm font-medium text-gray-700">Sort order</label>
                    <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $banner->sort_order) }}" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div class="flex items-center pt-8">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $banner->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="is_active" class="ml-2 text-sm text-gray-700">Active</label>
                </div>
            </div>
            <div class="flex gap-4">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Update banner</button>
                <a href="{{ route('admin.banners.index') }}" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50">Cancel</a>
            </div>
        </form>
    </div>
@endsection
