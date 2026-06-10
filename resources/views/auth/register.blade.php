@extends('layouts.auth')
@section('title','Daftar Akun')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-slate-900">Buat akun baru</h2>
    <p class="text-slate-500 mt-1">Mulai kelola kost Anda dengan MyKostApp</p>
</div>
<form method="POST" action="{{ route('register') }}" class="space-y-5">
    @csrf
    <div class="grid grid-cols-2 gap-4">
        <div class="col-span-2">
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name') }}" required
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-400 @enderror"
                   placeholder="Nama Anda">
            @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div class="col-span-2">
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-400 @enderror"
                   placeholder="email@contoh.com">
            @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
            <input type="password" name="password" required
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password') border-red-400 @enderror"
                   placeholder="••••••••">
            @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Konfirmasi Password</label>
            <input type="password" name="password_confirmation" required
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                   placeholder="••••••••">
        </div>
        <div class="col-span-2">
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Nomor HP <span class="text-slate-400">(opsional)</span></label>
            <input type="text" name="phone" value="{{ old('phone') }}"
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                   placeholder="08xxxxxxxxxx">
        </div>
    </div>
    <button type="submit"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-4 rounded-xl transition-all text-sm shadow-lg shadow-blue-600/20">
        Buat Akun
    </button>
    <p class="text-center text-sm text-slate-500">
        Sudah punya akun? <a href="{{ route('login') }}" class="font-semibold text-blue-600 hover:text-blue-700">Masuk sekarang</a>
    </p>
</form>
@endsection
