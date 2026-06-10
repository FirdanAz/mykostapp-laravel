<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Login') — MyKostApp</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-inter antialiased">
<div class="min-h-screen flex">

    {{-- Left Panel (Decorative) --}}
    <div class="hidden lg:flex lg:w-1/2 xl:w-3/5 relative bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 items-center justify-center p-12">
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-white/5 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-white/5 rounded-full blur-3xl"></div>
        </div>
        <div class="relative z-10 text-center text-white max-w-lg">
            <div class="w-20 h-20 bg-white/10 backdrop-blur-sm rounded-3xl flex items-center justify-center mx-auto mb-8 border border-white/20">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </div>
            <h1 class="text-4xl font-bold mb-4 leading-tight">Kelola Kost<br>Lebih Mudah & Modern</h1>
            <p class="text-blue-100 text-lg leading-relaxed mb-10">
                Satu platform untuk manajemen kamar, penghuni, tagihan, dan laporan keuangan kost Anda.
            </p>
            <div class="grid grid-cols-3 gap-4 text-center">
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-4 border border-white/10">
                    <p class="text-2xl font-bold">100%</p>
                    <p class="text-xs text-blue-200 mt-1">Gratis & Open Source</p>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-4 border border-white/10">
                    <p class="text-2xl font-bold">10+</p>
                    <p class="text-xs text-blue-200 mt-1">Fitur Lengkap</p>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-4 border border-white/10">
                    <p class="text-2xl font-bold">24/7</p>
                    <p class="text-xs text-blue-200 mt-1">Akses Kapan Saja</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Right Panel (Form) --}}
    <div class="flex-1 flex items-center justify-center p-6 sm:p-12 bg-white">
        <div class="w-full max-w-md">

            {{-- Logo mobile --}}
            <div class="flex items-center gap-3 mb-8 lg:hidden">
                <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
                <p class="font-bold text-slate-800 text-xl">MyKostApp</p>
            </div>

            {{-- Session alerts --}}
            @if(session('success'))
            <div class="mb-6 flex items-center gap-3 bg-green-50 border border-green-200 rounded-xl p-4">
                <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <p class="text-sm text-green-700">{{ session('success') }}</p>
            </div>
            @endif
            @if($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">
                <ul class="text-sm text-red-700 space-y-1">
                    @foreach($errors->all() as $error)
                    <li class="flex items-center gap-2">
                        <span class="w-1 h-1 bg-red-500 rounded-full flex-shrink-0"></span>
                        {{ $error }}
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            @yield('content')
        </div>
    </div>
</div>
</body>
</html>
