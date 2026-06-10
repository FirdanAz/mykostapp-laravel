@extends('layouts.app')
@section('title','Ubah Password')
@section('page-title','Ubah Password')
@section('breadcrumb') <span class="mx-1">/</span> <a href="{{ route('profile.edit') }}" class="hover:text-slate-600">Profil</a> <span class="mx-1">/</span> Ubah Password @endsection

@section('content')
<div class="max-w-md">
<div class="bg-white rounded-2xl border border-slate-200 p-6">
    <div class="pb-4 border-b border-slate-100 mb-5">
        <h3 class="font-semibold text-slate-800">Ubah Password</h3>
        <p class="text-xs text-slate-400 mt-0.5">Pastikan password baru Anda kuat dan mudah diingat</p>
    </div>
    <form method="POST" action="{{ route('profile.password.update') }}" class="space-y-5">
        @csrf @method('PUT')
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Password Saat Ini <span class="text-red-500">*</span></label>
            <input type="password" name="current_password" required
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('current_password') border-red-400 @enderror"
                   placeholder="••••••••">
            @error('current_password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Password Baru <span class="text-red-500">*</span></label>
            <input type="password" name="password" required
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-400 @enderror"
                   placeholder="Min. 8 karakter">
            @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Konfirmasi Password Baru <span class="text-red-500">*</span></label>
            <input type="password" name="password_confirmation" required
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                   placeholder="Ulangi password baru">
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl transition-all shadow-lg shadow-blue-600/20">
                Ubah Password
            </button>
            <a href="{{ route('profile.edit') }}" class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl transition-colors">
                Batal
            </a>
        </div>
    </form>
</div>
</div>
@endsection
