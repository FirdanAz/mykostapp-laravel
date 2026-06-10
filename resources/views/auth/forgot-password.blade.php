@extends('layouts.auth')
@section('title','Lupa Password')
@section('content')
<div class="mb-8">
    <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-700 mb-6">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali ke Login
    </a>
    <h2 class="text-2xl font-bold text-slate-900">Lupa password?</h2>
    <p class="text-slate-500 mt-1">Masukkan email Anda dan kami akan mengirimkan link reset password.</p>
</div>
<form method="POST" action="{{ route('password.email') }}" class="space-y-5">
    @csrf
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required
               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-400 @enderror"
               placeholder="email@contoh.com">
        @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>
    <button type="submit"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-4 rounded-xl transition-all text-sm shadow-lg shadow-blue-600/20">
        Kirim Link Reset Password
    </button>
</form>
@endsection
