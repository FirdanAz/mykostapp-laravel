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

{{-- Mobile sidebar backdrop --}}
<div x-show="sidebarOpen"
     x-transition:enter="transition-opacity ease-linear duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click="sidebarOpen = false"
     class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm lg:hidden"></div>

{{-- ═══════════════════════════════════════════════════════════════
     SIDEBAR
═══════════════════════════════════════════════════════════════ --}}
<aside class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 flex flex-col
              transform transition-transform duration-300 ease-in-out
              lg:translate-x-0"
       :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

    {{-- Logo --}}
    <div class="flex items-center gap-3 px-6 py-5 border-b border-slate-800">
        <div class="w-9 h-9 bg-blue-600 rounded-xl flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
        </div>
        <div>
            <p class="text-white font-bold text-sm leading-none">{{ \App\Models\Setting::get('app_name','MyKostApp') }}</p>
            <p class="text-slate-400 text-xs mt-0.5">Manajemen Kost</p>
        </div>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto scrollbar-none">

        @php
        $navItems = [
            ['route' => 'dashboard',         'icon' => 'grid',       'label' => 'Dashboard'],
            ['route' => 'kost.index',         'icon' => 'building',   'label' => 'Data Kost'],
            ['route' => 'rooms.index',        'icon' => 'door-open',  'label' => 'Kamar'],
            ['route' => 'tenants.index',      'icon' => 'users',      'label' => 'Penghuni'],
            ['route' => 'invoices.index',     'icon' => 'receipt',    'label' => 'Tagihan'],
            ['route' => 'payments.index',     'icon' => 'credit-card','label' => 'Pembayaran'],
            ['route' => 'complaints.index',   'icon' => 'chat',       'label' => 'Keluhan'],
            ['route' => 'reports.index',      'icon' => 'chart',      'label' => 'Laporan'],
            ['route' => 'settings.index',     'icon' => 'cog',        'label' => 'Pengaturan'],
        ];
        @endphp

        @foreach($navItems as $item)
        @php $isActive = request()->routeIs($item['route'].'*'); @endphp
        <a href="{{ route($item['route']) }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-150 group
                  {{ $isActive
                      ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20'
                      : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
            <span class="w-5 h-5 flex-shrink-0">
                @include('layouts.partials.icon', ['name' => $item['icon'], 'active' => $isActive])
            </span>
            {{ $item['label'] }}
            @if($item['route'] === 'invoices.index')
            @php $unpaid = \App\Models\Invoice::whereIn('status',['unpaid','overdue'])->count(); @endphp
            @if($unpaid > 0)
            <span class="ml-auto bg-amber-500 text-white text-xs font-bold px-1.5 py-0.5 rounded-full">{{ $unpaid }}</span>
            @endif
            @endif
            @if($item['route'] === 'complaints.index')
            @php $active = \App\Models\Complaint::whereIn('status',['new','in_progress'])->count(); @endphp
            @if($active > 0)
            <span class="ml-auto bg-red-500 text-white text-xs font-bold px-1.5 py-0.5 rounded-full">{{ $active }}</span>
            @endif
            @endif
        </a>
        @endforeach
    </nav>

    {{-- User section --}}
    <div class="px-3 py-4 border-t border-slate-800">
        <a href="{{ route('profile.edit') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-800 transition-colors group">
            <img src="{{ auth()->user()->avatar_url }}" alt="" class="w-8 h-8 rounded-full object-cover ring-2 ring-slate-700">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                <p class="text-xs text-slate-400 truncate">{{ auth()->user()->email }}</p>
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

{{-- ═══════════════════════════════════════════════════════════════
     MAIN CONTENT
═══════════════════════════════════════════════════════════════ --}}
<div class="lg:pl-64 flex flex-col min-h-screen">

    {{-- Top Navbar --}}
    <header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-slate-200 px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 gap-4">

            {{-- Mobile menu button --}}
            <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 rounded-lg text-slate-500 hover:bg-slate-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            {{-- Page title --}}
            <div class="flex-1">
                <h1 class="text-base font-semibold text-slate-800">@yield('page-title', 'Dashboard')</h1>
                @hasSection('breadcrumb')
                <div class="flex items-center gap-1 text-xs text-slate-400 mt-0.5">
                    <a href="{{ route('dashboard') }}" class="hover:text-slate-600">Home</a>
                    @yield('breadcrumb')
                </div>
                @endif
            </div>

            {{-- Right side --}}
            <div class="flex items-center gap-2">

                {{-- Notifications --}}
                <div class="relative" x-data="notifDropdown()" @click.outside="open=false">
                    <button @click="toggle()" class="relative p-2 rounded-lg text-slate-500 hover:bg-slate-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span x-show="count > 0"
                              x-text="count > 9 ? '9+' : count"
                              class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center px-1"></span>
                    </button>

                    <div x-show="open" x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                         class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl shadow-slate-200 border border-slate-100 overflow-hidden z-50">
                        <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100">
                            <h3 class="font-semibold text-slate-800 text-sm">Notifikasi</h3>
                            <button @click="markAllRead()" x-show="count > 0"
                                    class="text-xs text-blue-600 hover:text-blue-700 font-medium">Tandai semua dibaca</button>
                        </div>
                        <div class="max-h-80 overflow-y-auto">
                            <template x-if="notifications.length === 0">
                                <div class="px-4 py-8 text-center text-slate-400 text-sm">
                                    <svg class="w-8 h-8 mx-auto mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                    </svg>
                                    Tidak ada notifikasi baru
                                </div>
                            </template>
                            <template x-for="n in notifications" :key="n.id">
                                <a :href="n.url" class="flex gap-3 px-4 py-3 hover:bg-slate-50 border-b border-slate-50 transition-colors">
                                    <span class="text-xl flex-shrink-0 mt-0.5" x-text="n.icon"></span>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-slate-800 truncate" x-text="n.title"></p>
                                        <p class="text-xs text-slate-500 mt-0.5 line-clamp-2" x-text="n.message"></p>
                                        <p class="text-xs text-slate-400 mt-1" x-text="n.time"></p>
                                    </div>
                                </a>
                            </template>
                        </div>
                        <div class="px-4 py-2.5 border-t border-slate-100 bg-slate-50">
                            <a href="{{ route('notifications.index') }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">
                                Lihat semua notifikasi →
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Avatar --}}
                <div class="relative" x-data="{ open: false }" @click.outside="open=false">
                    <button @click="open=!open" class="flex items-center gap-2 p-1 rounded-lg hover:bg-slate-100 transition-colors">
                        <img src="{{ auth()->user()->avatar_url }}" alt="" class="w-8 h-8 rounded-full object-cover">
                        <svg class="w-4 h-4 text-slate-400 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open" x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                         class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-slate-100 overflow-hidden z-50">
                        <div class="px-4 py-3 border-b border-slate-100">
                            <p class="text-sm font-semibold text-slate-800">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-slate-400 truncate">{{ auth()->user()->email }}</p>
                        </div>
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Profil Saya
                        </a>
                        <a href="{{ route('profile.password') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                            </svg>
                            Ubah Password
                        </a>
                        <div class="border-t border-slate-100">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-2 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- Flash Messages --}}
    @if(session('success') || session('error') || session('warning'))
    <div id="flash-container" class="fixed top-4 right-4 z-[100] space-y-2 max-w-sm w-full">
        @if(session('success'))
        <div class="flash-toast flex items-start gap-3 bg-white border border-green-100 shadow-lg shadow-green-100/50 rounded-xl p-4">
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
        <div class="flash-toast flex items-start gap-3 bg-white border border-red-100 shadow-lg shadow-red-100/50 rounded-xl p-4">
            <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <div class="flex-1">
                <p class="text-sm font-semibold text-slate-800">Gagal!</p>
                <p class="text-sm text-slate-600 mt-0.5">{{ session('error') }}</p>
            </div>
            <button onclick="this.closest('.flash-toast').remove()" class="text-slate-300 hover:text-slate-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        @endif
    </div>
    <script>setTimeout(()=>document.getElementById('flash-container')?.remove(), 5000)</script>
    @endif

    {{-- Page Content --}}
    <main class="flex-1 px-4 sm:px-6 lg:px-8 py-6">
        @yield('content')
    </main>

    <footer class="px-6 py-4 border-t border-slate-200 bg-white">
        <p class="text-xs text-slate-400 text-center">
            © {{ date('Y') }} {{ \App\Models\Setting::get('app_name','MyKostApp') }} — Dibuat dengan ❤️
        </p>
    </footer>
</div>

@stack('scripts')
<script>
function notifDropdown() {
    return {
        open: false, count: 0, notifications: [],
        toggle() { this.open = !this.open; if(this.open) this.fetch(); },
        async fetch() {
            try {
                const r = await fetch('{{ route('notifications.unread') }}');
                const d = await r.json();
                this.notifications = d.notifications;
                this.count = d.count;
            } catch(e) {}
        },
        async markAllRead() {
            await fetch('{{ route('notifications.mark-all-read') }}', {
                method:'POST',
                headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'}
            });
            this.count = 0; this.notifications = []; this.open = false;
        },
        init() { this.fetch(); setInterval(()=>this.fetch(), 60000); }
    }
}
</script>
</body>
</html>
