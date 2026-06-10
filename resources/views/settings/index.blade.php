@extends('layouts.app')
@section('title','Pengaturan')
@section('page-title','Pengaturan Sistem')
@section('breadcrumb') <span class="mx-1">/</span> Pengaturan @endsection

@section('content')
<div class="max-w-3xl space-y-6">

{{-- System Settings --}}
<form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-5">
    @csrf @method('PATCH')

    <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-5">
        <div class="pb-3 border-b border-slate-100">
            <h3 class="font-semibold text-slate-800">Pengaturan Umum</h3>
            <p class="text-xs text-slate-400 mt-0.5">Konfigurasi dasar aplikasi MyKostApp</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Aplikasi</label>
                <input type="text" name="app_name" value="{{ old('app_name', $settings['app_name']) }}" required
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('app_name') border-red-400 @enderror"
                       placeholder="MyKostApp">
                @error('app_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Mata Uang</label>
                <select name="app_currency" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="IDR" {{ $settings['app_currency']=='IDR' ? 'selected':'' }}>IDR — Rupiah Indonesia</option>
                    <option value="USD" {{ $settings['app_currency']=='USD' ? 'selected':'' }}>USD — US Dollar</option>
                    <option value="MYR" {{ $settings['app_currency']=='MYR' ? 'selected':'' }}>MYR — Ringgit Malaysia</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Batas Jatuh Tempo Invoice (hari)</label>
                <input type="number" name="invoice_due_days" value="{{ old('invoice_due_days', $settings['invoice_due_days']) }}" required min="1" max="30"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('invoice_due_days') border-red-400 @enderror"
                       placeholder="10">
                <p class="text-xs text-slate-400 mt-1">Dihitung dari tanggal 1 tiap bulan</p>
                @error('invoice_due_days') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Nomor WhatsApp Admin</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm">+62</span>
                    <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number', $settings['whatsapp_number']) }}"
                           class="w-full pl-12 pr-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="812xxxxxxxx">
                </div>
                <p class="text-xs text-slate-400 mt-1">Untuk notifikasi & kontak penghuni</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Denda Keterlambatan (Rp)</label>
                <input type="number" name="late_fee" value="{{ old('late_fee', $settings['late_fee']) }}" min="0" step="1000"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="50000">
                <p class="text-xs text-slate-400 mt-1">0 = tidak ada denda</p>
            </div>
        </div>
    </div>

    <div class="flex gap-3">
        <button type="submit"
                class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl transition-all shadow-lg shadow-blue-600/20">
            Simpan Pengaturan
        </button>
    </div>
</form>

{{-- Info Kost --}}
<div class="bg-white rounded-2xl border border-slate-200 p-6">
    <div class="pb-3 border-b border-slate-100 mb-5">
        <h3 class="font-semibold text-slate-800">Informasi Kost</h3>
        <p class="text-xs text-slate-400 mt-0.5">Data kost yang ditampilkan di seluruh aplikasi</p>
    </div>
    <a href="{{ route('admin.kost.index') }}"
       class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-xl transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
        </svg>
        Edit Data Kost
    </a>
</div>

{{-- App Info --}}
<div class="bg-white rounded-2xl border border-slate-200 p-6">
    <div class="pb-3 border-b border-slate-100 mb-5">
        <h3 class="font-semibold text-slate-800">Informasi Sistem</h3>
    </div>
    <div class="grid grid-cols-2 gap-4 text-sm">
        <div class="p-3 bg-slate-50 rounded-xl">
            <p class="text-xs text-slate-400 mb-1">Versi Laravel</p>
            <p class="font-semibold text-slate-700">{{ app()->version() }}</p>
        </div>
        <div class="p-3 bg-slate-50 rounded-xl">
            <p class="text-xs text-slate-400 mb-1">Versi PHP</p>
            <p class="font-semibold text-slate-700">{{ PHP_VERSION }}</p>
        </div>
        <div class="p-3 bg-slate-50 rounded-xl">
            <p class="text-xs text-slate-400 mb-1">Environment</p>
            <p class="font-semibold text-slate-700">{{ app()->environment() }}</p>
        </div>
        <div class="p-3 bg-slate-50 rounded-xl">
            <p class="text-xs text-slate-400 mb-1">Timezone</p>
            <p class="font-semibold text-slate-700">{{ config('app.timezone') }}</p>
        </div>
    </div>
</div>

</div>
@endsection
