@extends('layouts.admin')

@section('title', 'Add product')
@section('heading', 'Add product')

@section('content')
    <div class="max-w-3xl">
        <a href="{{ route('admin.products.index') }}" class="text-sm text-indigo-600 hover:underline mb-4 inline-block">&larr; Back to products</a>
        <form method="POST" action="{{ route('admin.products.store') }}" class="bg-white rounded-lg shadow p-6 space-y-4">
            @csrf
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Name *</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="slug" class="block text-sm font-medium text-gray-700">Slug (leave blank to auto-generate)</label>
                <input type="text" name="slug" id="slug" value="{{ old('slug') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('slug')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="category_id" class="block text-sm font-medium text-gray-700">Category</label>
                <select name="category_id" id="category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">— None —</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" {{ old('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="sku" class="block text-sm font-medium text-gray-700">SKU</label>
                    <input type="text" name="sku" id="sku" value="{{ old('sku') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status *</label>
                    <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="archived" {{ old('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>
            </div>
            <div>
                <label for="short_description" class="block text-sm font-medium text-gray-700">Short description</label>
                <textarea name="short_description" id="short_description" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('short_description') }}</textarea>
            </div>
            <div>
                <label for="long_description" class="block text-sm font-medium text-gray-700">Long description</label>
                <textarea name="long_description" id="long_description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('long_description') }}</textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700">Price *</label>
                    <input type="number" name="price" id="price" value="{{ old('price', '0') }}" step="0.01" min="0" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('price')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="sale_price" class="block text-sm font-medium text-gray-700">Sale price</label>
                    <input type="number" name="sale_price" id="sale_price" value="{{ old('sale_price') }}" step="0.01" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>
            <div>
                <label for="stock_quantity" class="block text-sm font-medium text-gray-700">Stock quantity</label>
                <input type="number" name="stock_quantity" id="stock_quantity" value="{{ old('stock_quantity', '0') }}" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div class="flex gap-4">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Create product</button>
                <a href="{{ route('admin.products.index') }}" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50">Cancel</a>
            </div>
        </form>
    </div>
@endsection
