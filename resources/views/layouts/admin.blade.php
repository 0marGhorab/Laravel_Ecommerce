<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') – {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100" x-data="{ sidebarOpen: false }">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 z-40 w-64 bg-slate-800 text-white flex flex-col lg:static lg:translate-x-0 transform -translate-x-full lg:transform-none transition-transform duration-200 ease-in-out" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            <div class="flex items-center justify-between h-16 px-6 border-b border-slate-700">
                <a href="{{ route('admin.dashboard') }}" class="text-lg font-semibold text-white">
                    {{ config('app.name') }} Admin
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden p-2 rounded-md text-slate-400 hover:bg-slate-700 hover:text-white">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <nav class="flex-1 overflow-y-auto py-4 px-3">
                <ul class="space-y-1">
                    <li>
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white {{ request()->routeIs('admin.dashboard') ? 'bg-slate-700 text-white' : '' }}">
                            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white {{ request()->routeIs('admin.orders.*') ? 'bg-slate-700 text-white' : '' }}">
                            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            Orders
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.products.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white {{ request()->routeIs('admin.products.*') ? 'bg-slate-700 text-white' : '' }}">
                            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            Products
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white {{ request()->routeIs('admin.categories.*') ? 'bg-slate-700 text-white' : '' }}">
                            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                            Categories
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.banners.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white {{ request()->routeIs('admin.banners.*') ? 'bg-slate-700 text-white' : '' }}">
                            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Banners
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white {{ request()->routeIs('admin.users.*') ? 'bg-slate-700 text-white' : '' }}">
                            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            Users
                        </a>
                    </li>
                </ul>
            </nav>
            <div class="p-3 border-t border-slate-700">
                <a href="{{ route('products.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-400 hover:bg-slate-700 hover:text-white text-sm">
                    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    View Store
                </a>
            </div>
        </aside>

        <!-- Mobile sidebar overlay -->
        <div class="fixed inset-0 z-30 bg-black/50 lg:hidden" x-show="sidebarOpen" x-transition @click="sidebarOpen = false" x-cloak style="display: none;"></div>

        <!-- Main content -->
        <div class="flex-1 flex flex-col min-w-0">
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-md text-gray-500 hover:bg-gray-100">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <h1 class="text-lg font-semibold text-gray-900 truncate">@yield('heading', 'Admin')</h1>
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-gray-600">{{ auth()->user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-sm text-gray-500 hover:text-gray-700">Log out</button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                @if(session('success'))
                    <div class="mb-4 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">
                        {{ session('error') }}
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
    <script defer src="https://unpkg.com/alpinejs@3.14.3/dist/cdn.min.js"></script>
</body>
</html>
