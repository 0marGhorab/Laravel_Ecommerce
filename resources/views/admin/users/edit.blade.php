@extends('layouts.admin')

@section('title', 'Edit user')
@section('heading', 'Edit user')

@section('content')
    <div class="max-w-xl">
        <a href="{{ route('admin.users.index') }}" class="text-sm text-indigo-600 hover:underline mb-4 inline-block">&larr; Back to users</a>
        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="bg-white rounded-lg shadow p-6 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Name *</label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">New password (leave blank to keep)</label>
                <input type="password" name="password" id="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" autocomplete="new-password">
                @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm new password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" autocomplete="new-password">
            </div>
            @if($user->id !== auth()->id())
                <div class="flex items-center">
                    <input type="hidden" name="is_admin" value="0">
                    <input type="checkbox" name="is_admin" id="is_admin" value="1" {{ old('is_admin', $user->is_admin) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="is_admin" class="ml-2 text-sm text-gray-700">Administrator</label>
                </div>
            @endif
            <div class="flex gap-4">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Update user</button>
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50">Cancel</a>
            </div>
        </form>
    </div>
@endsection
