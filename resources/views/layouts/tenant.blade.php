<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ \App\Models\Setting::get('app_name','MyKostApp') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="h-full bg-slate-50 font-inter antialiased" x-data="{ sidebarOpen: false }">

<div x-show="sidebarOpen"
     x-transition:enter="transition-opacity ease-linear duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click="sidebarOpen = false"
     class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm lg:hidden"></div>

{{-- SIDEBAR --}}
<aside class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 flex flex-col
              transform transition-transform duration-300 ease-in-out
              lg:translate-x-0"
       :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

    <div class="flex items-center gap-3 px-6 py-5 border-b border-slate-800">
        <div class="w-9 h-9 bg-emerald-600 rounded-xl flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
        </div>
        <div>
            <p class="text-white font-bold text-sm leading-none">{{ \App\Models\Setting::get('app_name','MyKostApp') }}</p>
            <p class="text-emerald-400 text-xs mt-0.5">Portal Penyewa</p>
        </div>
    </div>

    <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
        @php
        $tenantNav = [
            ['route' => 'tenant.dashboard',        'label' => 'Dashboard',     'icon' => 'grid'],
            ['route' => 'tenant.invoices.index',   'label' => 'Tagihan Saya',  'icon' => 'receipt'],
            ['route' => 'tenant.complaints.index', 'label' => 'Keluhan Saya',  'icon' => 'chat'],
            ['route' => 'tenant.applications.index', 'label' => 'Pengajuan Sewa', 'icon' => 'folder-open'],
        ];
        @endphp

        @foreach($tenantNav as $item)
        @php $isActive = request()->routeIs($item['route'].'*'); @endphp
        <a href="{{ route($item['route']) }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-150
                  {{ $isActive ? 'bg-emerald-600 text-white shadow-lg' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
            <span class="w-5 h-5 flex-shrink-0">
                @include('layouts.partials.icon', ['name' => $item['icon'], 'active' => $isActive])
            </span>
            {{ $item['label'] }}
        </a>
        @endforeach

        <div class="pt-3 mt-3 border-t border-slate-800">
            <a href="{{ route('public.kosts.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-400 hover:bg-slate-800 hover:text-white transition-all">
                <span class="w-5 h-5">
                    @include('layouts.partials.icon', ['name' => 'search', 'active' => false])
                </span>
                Cari Kos
            </a>
        </div>
    </nav>

    <div class="px-3 py-4 border-t border-slate-800">
        <a href="{{ route('profile.edit') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-800 transition-colors group">
            <img src="{{ auth()->user()->avatar_url }}" alt="" class="w-8 h-8 rounded-full object-cover ring-2 ring-slate-700">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                <p class="text-xs text-emerald-400 truncate">Penyewa</p>
            </div>
        </a>
        <form method="POST" action="{{ route('logout') }}" class="mt-1">
            @csrf
            <button type="submit"
                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-400 hover:bg-red-500/10 hover:text-red-400 transition-colors text-sm font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Logout
            </button>
        </form>
    </div>
</aside>

{{-- MAIN CONTENT --}}
<div class="lg:pl-64 flex flex-col min-h-screen">

    <header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-slate-200 px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 gap-4">
            <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 rounded-lg text-slate-500 hover:bg-slate-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            <div class="flex-1">
                <h1 class="text-base font-semibold text-slate-800">@yield('page-title', 'Dashboard')</h1>
                @hasSection('breadcrumb')
                <div class="flex items-center gap-1 text-xs text-slate-400 mt-0.5">
                    <a href="{{ route('tenant.dashboard') }}" class="hover:text-slate-600">Home</a>
                    @yield('breadcrumb')
                </div>
                @endif
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('profile.edit') }}"
                   class="flex items-center gap-2 p-1 rounded-lg hover:bg-slate-100 transition-colors">
                    <img src="{{ auth()->user()->avatar_url }}" alt="" class="w-8 h-8 rounded-full object-cover">
                </a>
            </div>
        </div>
    </header>

    {{-- Flash Messages --}}
    @if(session('success') || session('error') || session('info'))
    <div id="flash-container" class="fixed top-4 right-4 z-[100] space-y-2 max-w-sm w-full">
        @if(session('success'))
        <div class="flash-toast flex items-start gap-3 bg-white border border-green-100 shadow-lg rounded-xl p-4">
            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div class="flex-1">
                <p class="text-sm font-semibold text-slate-800">Berhasil!</p>
                <p class="text-sm text-slate-600 mt-0.5">{{ session('success') }}</p>
            </div>
            <button onclick="this.closest('.flash-toast').remove()" class="text-slate-300 hover:text-slate-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        @endif
        @if(session('error'))
        <div class="flash-toast flex items-start gap-3 bg-white border border-red-100 shadow-lg rounded-xl p-4">
            <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <div class="flex-1">
                <p class="text-sm font-semibold text-slate-800">Gagal!</p>
                <p class="text-sm text-slate-600 mt-0.5">{{ session('error') }}</p>
            </div>
        </div>
        @endif
        @if(session('info'))
        <div class="flash-toast flex items-start gap-3 bg-white border border-blue-100 shadow-lg rounded-xl p-4">
            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="flex-1">
                <p class="text-sm font-semibold text-slate-800">Info</p>
                <p class="text-sm text-slate-600 mt-0.5">{{ session('info') }}</p>
            </div>
        </div>
        @endif
    </div>
    <script>setTimeout(()=>document.getElementById('flash-container')?.remove(), 5000)</script>
    @endif

    <main class="flex-1 px-4 sm:px-6 lg:px-8 py-6">
        @yield('content')
    </main>

    <footer class="px-6 py-4 border-t border-slate-200 bg-white">
        <p class="text-xs text-slate-400 text-center">
            © {{ date('Y') }} {{ \App\Models\Setting::get('app_name','MyKostApp') }} — Portal Penyewa
        </p>
    </footer>
</div>

@stack('scripts')
</body>
</html>
