@extends('layouts.admin')

@section('title', 'Users')
@section('heading', 'Users')

@section('content')
    <div class="bg-white rounded-lg shadow">
        <div class="p-4 border-b border-gray-200">
            <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or email..." class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                <button type="submit" class="px-3 py-1.5 bg-gray-800 text-white text-sm rounded-md hover:bg-gray-700">Search</button>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Admin</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Registered</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $user->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($user->is_admin)
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-indigo-100 text-indigo-800">Admin</span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->created_at->format('M j, Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <a href="{{ route('admin.users.edit', $user) }}" class="text-indigo-600 hover:text-indigo-800 mr-3">Edit</a>
                                @if($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline" onsubmit="return confirm('Delete this user?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                                    </form>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="px-6 py-3 border-t border-gray-200">
                {{ $users->links() }}
            </div>
        @endif
    </div>
@endsection
