@extends('layouts.auth')
@section('title','Reset Password')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-slate-900">Reset password</h2>
    <p class="text-slate-500 mt-1">Masukkan password baru Anda.</p>
</div>
<form method="POST" action="{{ route('password.update') }}" class="space-y-5">
    @csrf
    <input type="hidden" name="token" value="{{ $request->route('token') }}">
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
        <input type="email" name="email" value="{{ old('email', $request->email) }}" required
               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-400 @enderror">
        @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Password Baru</label>
        <input type="password" name="password" required
               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-400 @enderror"
               placeholder="Minimal 8 karakter">
        @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Konfirmasi Password Baru</label>
        <input type="password" name="password_confirmation" required
               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
               placeholder="Ulangi password baru">
    </div>
    <button type="submit"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-4 rounded-xl transition-all text-sm shadow-lg shadow-blue-600/20">
        Reset Password
    </button>
</form>
@endsection
