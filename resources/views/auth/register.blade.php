@extends('layouts.auth')
@section('title','Daftar Akun')
@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-slate-900">Buat akun baru</h2>
    <p class="text-slate-500 mt-1 text-sm">Bergabung dengan MyKostApp sekarang</p>
</div>

<form method="POST" action="{{ route('register') }}" class="space-y-5">
    @csrf

    {{-- ── Pilihan Tipe Akun ──────────────────────────────── --}}
    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-2">Saya adalah <span class="text-red-500">*</span></label>
        @error('role') <p class="mb-2 text-xs text-red-600">{{ $message }}</p> @enderror
        <div class="grid grid-cols-2 gap-3">

            {{-- Admin / Pemilik Kos --}}
            <label id="role-admin-label" class="role-card flex flex-col items-center gap-2 p-4 rounded-xl border-2 cursor-pointer transition-all
                   {{ old('role') === 'admin' || !old('role') ? 'border-blue-500 bg-blue-50' : 'border-slate-200 hover:border-blue-300' }}"
                   for="role_admin">
                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
                <div class="text-center">
                    <p class="font-semibold text-sm text-slate-800">Pemilik Kos</p>
                    <p class="text-xs text-slate-500 mt-0.5">Kelola kos & penghuni</p>
                </div>
                <input type="radio" id="role_admin" name="role" value="admin"
                       {{ old('role', 'admin') === 'admin' ? 'checked' : '' }} class="sr-only">
            </label>

            {{-- Tenant / Pencari Kos --}}
            <label id="role-tenant-label" class="role-card flex flex-col items-center gap-2 p-4 rounded-xl border-2 cursor-pointer transition-all
                   {{ old('role') === 'tenant' ? 'border-emerald-500 bg-emerald-50' : 'border-slate-200 hover:border-emerald-300' }}"
                   for="role_tenant">
                <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div class="text-center">
                    <p class="font-semibold text-sm text-slate-800">Pencari Kos</p>
                    <p class="text-xs text-slate-500 mt-0.5">Cari & sewa kos</p>
                </div>
                <input type="radio" id="role_tenant" name="role" value="tenant"
                       {{ old('role') === 'tenant' ? 'checked' : '' }} class="sr-only">
            </label>
        </div>
    </div>

    {{-- ── Form Fields ───────────────────────────────────── --}}
    <div class="grid grid-cols-2 gap-4">
        <div class="col-span-2">
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" required
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-400 @enderror"
                   placeholder="Nama Anda">
            @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="col-span-2">
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Email <span class="text-red-500">*</span></label>
            <input type="email" name="email" value="{{ old('email') }}" required
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-400 @enderror"
                   placeholder="email@contoh.com">
            @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Password <span class="text-red-500">*</span></label>
            <input type="password" name="password" required
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password') border-red-400 @enderror"
                   placeholder="Min. 8 karakter">
            @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Konfirmasi Password <span class="text-red-500">*</span></label>
            <input type="password" name="password_confirmation" required
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                   placeholder="••••••••">
        </div>

        <div class="col-span-2">
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Nomor HP <span class="text-slate-400 font-normal">(opsional)</span></label>
            <input type="text" name="phone" value="{{ old('phone') }}"
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                   placeholder="08xxxxxxxxxx">
        </div>
    </div>

    <button type="submit"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-4 rounded-xl transition-all text-sm shadow-lg shadow-blue-600/20 flex items-center justify-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
        </svg>
        Buat Akun
    </button>

    <p class="text-center text-sm text-slate-500">
        Sudah punya akun? <a href="{{ route('login') }}" class="font-semibold text-blue-600 hover:text-blue-700">Masuk sekarang</a>
    </p>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const adminLabel  = document.getElementById('role-admin-label');
    const tenantLabel = document.getElementById('role-tenant-label');
    const adminRadio  = document.getElementById('role_admin');
    const tenantRadio = document.getElementById('role_tenant');

    function updateUI() {
        if (adminRadio.checked) {
            // Admin Card Active
            adminLabel.classList.add('border-blue-500', 'bg-blue-50');
            adminLabel.classList.remove('border-slate-200', 'hover:border-blue-300');

            // Tenant Card Inactive
            tenantLabel.classList.add('border-slate-200', 'hover:border-emerald-300');
            tenantLabel.classList.remove('border-emerald-500', 'bg-emerald-50');
        } else {
            // Tenant Card Active
            tenantLabel.classList.add('border-emerald-500', 'bg-emerald-50');
            tenantLabel.classList.remove('border-slate-200', 'hover:border-emerald-300');

            // Admin Card Inactive
            adminLabel.classList.add('border-slate-200', 'hover:border-blue-300');
            adminLabel.classList.remove('border-blue-500', 'bg-blue-50');
        }
    }

    adminRadio.addEventListener('change', updateUI);
    tenantRadio.addEventListener('change', updateUI);
    
    updateUI();
});
</script>
@endpush
@endsection
