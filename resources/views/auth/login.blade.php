@extends('layouts.auth')
@section('title', 'Login')
@section('content')

<div class="mb-8">
    <h2 class="text-2xl font-bold text-slate-900">Selamat datang kembali!</h2>
    <p class="text-slate-500 mt-1">Masuk ke akun MyKostApp Anda</p>
</div>

<form method="POST" action="{{ route('login') }}" class="space-y-5">
    @csrf

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required autofocus
               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-900 text-sm
                      focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                      placeholder:text-slate-400 transition
                      @error('email') border-red-400 focus:ring-red-400 @enderror"
               placeholder="admin@mykost.com">
        @error('email') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <div class="flex items-center justify-between mb-1.5">
            <label class="block text-sm font-medium text-slate-700">Password</label>
            <a href="{{ route('password.request') }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">Lupa password?</a>
        </div>
        <div class="relative">
            <input type="password" name="password" id="password" required
                   class="w-full px-4 py-2.5 pr-11 rounded-xl border border-slate-300 bg-white text-slate-900 text-sm
                          focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                          placeholder:text-slate-400 transition
                          @error('password') border-red-400 focus:ring-red-400 @enderror"
                   placeholder="••••••••">
            <button type="button" onclick="togglePass()"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                <svg id="eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </button>
        </div>
        @error('password') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="flex items-center">
        <input type="checkbox" name="remember" id="remember" class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
        <label for="remember" class="ml-2 text-sm text-slate-600">Ingat saya</label>
    </div>

    <button type="submit"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-4 rounded-xl
                   transition-all duration-150 text-sm shadow-lg shadow-blue-600/20 hover:shadow-blue-600/30
                   focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
        Masuk ke Dashboard
    </button>

    <p class="text-center text-sm text-slate-500">
        Belum punya akun?
        <a href="{{ route('register') }}" class="font-semibold text-blue-600 hover:text-blue-700">Daftar sekarang</a>
    </p>
</form>

<div class="mt-6 p-4 bg-slate-50 rounded-xl border border-slate-200">
    <p class="text-xs font-semibold text-slate-500 mb-2 uppercase tracking-wide">Demo Credentials</p>
    <p class="text-xs text-slate-600">Email: <span class="font-mono font-medium">admin@mykost.com</span></p>
    <p class="text-xs text-slate-600">Password: <span class="font-mono font-medium">password123</span></p>
</div>

<script>
function togglePass() {
    const p = document.getElementById('password');
    p.type = p.type === 'password' ? 'text' : 'password';
}
</script>
@endsection
