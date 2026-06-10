@extends('layouts.tenant')
@section('title', 'Dashboard Penyewa')
@section('page-title', 'Dashboard Saya')

@section('content')
<div class="space-y-6">

{{-- Selamat Datang --}}
<div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-2xl p-6 text-white">
    <div class="flex items-center gap-4">
        <img src="{{ auth()->user()->avatar_url }}" alt="" class="w-14 h-14 rounded-full object-cover ring-4 ring-white/20">
        <div>
            <p class="text-emerald-100 text-sm">Selamat datang kembali,</p>
            <h2 class="text-xl font-bold">{{ auth()->user()->name }}</h2>
            <p class="text-emerald-200 text-sm mt-0.5">Penyewa Kos</p>
        </div>
    </div>
</div>

@if(!$tenantProfile)
{{-- Belum terdaftar sebagai penghuni --}}
<div class="bg-white rounded-2xl border border-slate-200 p-8 text-center">
    <div class="w-16 h-16 bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
    </div>
    <h3 class="text-lg font-semibold text-slate-800 mb-2">Belum Terdaftar sebagai Penghuni</h3>
    <p class="text-slate-500 text-sm max-w-sm mx-auto mb-6">
        Akun Anda belum terhubung dengan kamar kos manapun. Silakan hubungi pemilik kos untuk mendaftarkan Anda sebagai penghuni.
    </p>
    <a href="{{ route('public.kosts.index') }}"
       class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-xl text-sm transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        Cari Kos Sekarang
    </a>
</div>

@if($rentalApplications->isNotEmpty())
<div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4 shadow-sm">
    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/>
            </svg>
            Status Pengajuan Sewa Terbaru
        </h3>
        <a href="{{ route('tenant.applications.index') }}" class="text-xs text-emerald-600 font-semibold hover:text-emerald-700">Lihat Semua →</a>
    </div>
    <div class="divide-y divide-slate-100">
        @foreach($rentalApplications as $app)
        <div class="flex items-center justify-between py-3">
            <div>
                <p class="text-sm font-semibold text-slate-800">{{ $app->room->kost->name ?? 'Kos' }} — Kamar {{ $app->room->number }}</p>
                <p class="text-xs text-slate-400 mt-0.5">Mulai: {{ $app->start_date?->format('d M Y') }} • Durasi: {{ $app->duration_months }} Bulan</p>
            </div>
            <div class="text-right">
                @php
                    $statusColors = [
                        'pending' => 'bg-amber-100 text-amber-800',
                        'approved' => 'bg-emerald-100 text-emerald-800',
                        'rejected' => 'bg-red-100 text-red-800',
                    ];
                @endphp
                <span class="text-xs px-2.5 py-1 rounded-full font-semibold {{ $statusColors[$app->status] ?? 'bg-slate-100 text-slate-600' }}">
                    {{ $app->status_label }}
                </span>
                @if($app->status === 'rejected')
                <p class="text-[10px] text-red-500 mt-1 max-w-[180px] break-words">{{ $app->rejection_reason }}</p>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif
@else

{{-- Info Kamar --}}
<div class="bg-white rounded-2xl border border-slate-200 p-6">
    <h3 class="font-semibold text-slate-800 mb-4 flex items-center gap-2">
        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        Kamar Saya
    </h3>
    @if($room)
    <div class="flex items-center gap-4">
        @if($room->first_photo)
        <img src="{{ $room->first_photo }}" alt="" class="w-24 h-20 object-cover rounded-xl flex-shrink-0">
        @else
        <div class="w-24 h-20 bg-slate-100 rounded-xl flex items-center justify-center flex-shrink-0">
            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>
        @endif
        <div>
            <div class="flex items-center gap-2">
                <span class="text-sm font-semibold text-slate-800">{{ $room->kost->name ?? 'Kos' }}</span>
                <span class="text-xs px-2 py-0.5 bg-emerald-100 text-emerald-700 rounded-full">Aktif</span>
            </div>
            <p class="text-2xl font-bold text-slate-900 mt-0.5">Kamar {{ $room->number }}</p>
            <p class="text-sm text-slate-500">Lantai {{ $room->floor }} • {{ $room->formatted_price }}/bulan</p>
            <p class="text-xs text-slate-400 mt-1">Mulai {{ $tenantProfile->start_date?->format('d M Y') }}</p>
        </div>
    </div>
    @else
    <p class="text-slate-400 text-sm">Informasi kamar tidak tersedia.</p>
    @endif
</div>

{{-- Stats --}}
<div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
    <div class="bg-white rounded-2xl border border-slate-200 p-5">
        <p class="text-3xl font-bold text-amber-600">{{ $unpaidInvoices->count() }}</p>
        <p class="text-sm font-medium text-slate-600 mt-1">Tagihan Belum Lunas</p>
        <a href="{{ route('tenant.invoices.index') }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium mt-1 inline-block">Lihat →</a>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-5">
        <p class="text-3xl font-bold text-red-500">{{ $openComplaints }}</p>
        <p class="text-sm font-medium text-slate-600 mt-1">Keluhan Aktif</p>
        <a href="{{ route('tenant.complaints.index') }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium mt-1 inline-block">Lihat →</a>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-5 col-span-2 sm:col-span-1">
        <p class="text-3xl font-bold text-emerald-600">{{ $recentPayments->count() }}</p>
        <p class="text-sm font-medium text-slate-600 mt-1">Pembayaran Terakhir</p>
    </div>
</div>

{{-- Tagihan Menunggu --}}
@if($unpaidInvoices->count() > 0)
<div class="bg-white rounded-2xl border border-slate-200">
    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
        <h3 class="font-semibold text-slate-800 text-sm">Tagihan Perlu Dibayar</h3>
        <a href="{{ route('tenant.invoices.index') }}" class="text-xs text-blue-600 font-medium">Lihat semua →</a>
    </div>
    <div class="divide-y divide-slate-100">
        @foreach($unpaidInvoices as $invoice)
        <div class="flex items-center gap-4 px-6 py-4">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-slate-800">{{ $invoice->invoice_number }}</p>
                <p class="text-xs text-slate-400 mt-0.5">
                    {{ $invoice->period_start?->format('M Y') }}
                    @if($invoice->status === 'overdue') <span class="text-red-500 font-medium">• Jatuh Tempo!</span> @endif
                </p>
            </div>
            <div class="text-right">
                <p class="text-sm font-bold text-slate-900">{{ $invoice->formatted_amount }}</p>
                @php $sc=['unpaid'=>'bg-amber-100 text-amber-700','overdue'=>'bg-red-100 text-red-700','pending_verification'=>'bg-blue-100 text-blue-700']; @endphp
                <span class="text-xs px-2 py-0.5 rounded-full {{ $sc[$invoice->status] ?? 'bg-slate-100 text-slate-600' }}">
                    {{ $invoice->status === 'pending_verification' ? 'Menunggu Verifikasi' : ($invoice->status === 'overdue' ? 'Terlambat' : 'Belum Bayar') }}
                </span>
            </div>
            @if(in_array($invoice->status, ['unpaid', 'overdue']))
            <a href="{{ route('tenant.payments.upload', $invoice) }}"
               class="flex-shrink-0 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-medium rounded-lg transition-colors">
                Bayar
            </a>
            @endif
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Quick Actions --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <a href="{{ route('tenant.invoices.index') }}"
       class="flex items-center gap-4 bg-white border border-slate-200 rounded-2xl p-4 hover:shadow-md transition-shadow group">
        <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center group-hover:bg-blue-100 transition-colors">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <div>
            <p class="font-semibold text-slate-800">Riwayat Tagihan</p>
            <p class="text-xs text-slate-400 mt-0.5">Lihat & bayar tagihan</p>
        </div>
    </a>
    <a href="{{ route('tenant.complaints.create') }}"
       class="flex items-center gap-4 bg-white border border-slate-200 rounded-2xl p-4 hover:shadow-md transition-shadow group">
        <div class="w-12 h-12 bg-orange-50 rounded-xl flex items-center justify-center group-hover:bg-orange-100 transition-colors">
            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
        </div>
        <div>
            <p class="font-semibold text-slate-800">Buat Keluhan</p>
            <p class="text-xs text-slate-400 mt-0.5">Laporkan masalah ke admin</p>
        </div>
    </a>
</div>
@endif

</div>
@endsection
