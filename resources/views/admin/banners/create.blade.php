@extends('layouts.admin')

@section('title', 'Add banner')
@section('heading', 'Add banner')

@section('content')
    <div class="max-w-2xl">
        <a href="{{ route('admin.banners.index') }}" class="text-sm text-indigo-600 hover:underline mb-4 inline-block">&larr; Back to banners</a>
        <form method="POST" action="{{ route('admin.banners.store') }}" enctype="multipart/form-data" class="bg-white rounded-lg shadow p-6 space-y-4">
            @csrf
            <div>
                <label for="image" class="block text-sm font-medium text-gray-700">Background image *</label>
                <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" required class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-indigo-50 file:text-indigo-700">
                <p class="mt-1 text-xs text-gray-500">JPEG, PNG, GIF or WebP. Max 2MB. Used as full-width background on product page.</p>
                @error('image')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label for="subtitle" class="block text-sm font-medium text-gray-700">Subtitle</label>
                <input type="text" name="subtitle" id="subtitle" value="{{ old('subtitle') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="cta_text" class="block text-sm font-medium text-gray-700">Button text</label>
                    <input type="text" name="cta_text" id="cta_text" value="{{ old('cta_text') }}" placeholder="e.g. Shop now" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="cta_url" class="block text-sm font-medium text-gray-700">Button URL</label>
                    <input type="text" name="cta_url" id="cta_url" value="{{ old('cta_url') }}" placeholder="/ or https://..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="sort_order" class="block text-sm font-medium text-gray-700">Sort order</label>
                    <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div class="flex items-center pt-8">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="is_active" class="ml-2 text-sm text-gray-700">Active</label>
                </div>
            </div>
            <div class="flex gap-4">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Create banner</button>
                <a href="{{ route('admin.banners.index') }}" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50">Cancel</a>
            </div>
        </form>
    </div>
@endsection
