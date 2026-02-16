@extends('layouts.admin')

@section('title', 'Add category')
@section('heading', 'Add category')

@section('content')
    <div class="max-w-xl">
        <a href="{{ route('admin.categories.index') }}" class="text-sm text-indigo-600 hover:underline mb-4 inline-block">&larr; Back to categories</a>
        <form method="POST" action="{{ route('admin.categories.store') }}" class="bg-white rounded-lg shadow p-6 space-y-4">
            @csrf
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Name *</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="slug" class="block text-sm font-medium text-gray-700">Slug (leave blank to auto-generate)</label>
                <input type="text" name="slug" id="slug" value="{{ old('slug') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label for="parent_id" class="block text-sm font-medium text-gray-700">Parent category</label>
                <select name="parent_id" id="parent_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">— None —</option>
                    @foreach($parentCategories as $c)
                        <option value="{{ $c->id }}" {{ old('parent_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" id="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
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
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Create category</button>
                <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50">Cancel</a>
            </div>
        </form>
    </div>
@endsection
