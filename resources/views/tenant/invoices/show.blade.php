@extends('layouts.tenant')
@section('title', 'Detail Tagihan')
@section('page-title', 'Detail Tagihan')
@section('breadcrumb')
    <span class="mx-1">/</span> <a href="{{ route('tenant.invoices.index') }}" class="hover:text-slate-600">Tagihan</a>
    <span class="mx-1">/</span> {{ $invoice->invoice_number }}
@endsection

@section('content')
<div class="max-w-2xl space-y-5">

{{-- Invoice Card --}}
<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
    <div class="bg-gradient-to-r from-slate-800 to-slate-700 px-6 py-5 text-white">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-slate-400 text-xs uppercase tracking-wide">Invoice</p>
                <p class="text-xl font-bold mt-1">{{ $invoice->invoice_number }}</p>
            </div>
            @php
            $sc=['unpaid'=>'bg-amber-400 text-amber-900','overdue'=>'bg-red-400 text-white','pending_verification'=>'bg-blue-400 text-white','paid'=>'bg-green-400 text-white'];
            $sl=['unpaid'=>'Belum Dibayar','overdue'=>'Terlambat','pending_verification'=>'Menunggu Verifikasi','paid'=>'Lunas'];
            @endphp
            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $sc[$invoice->status] ?? 'bg-slate-400 text-white' }}">
                {{ $sl[$invoice->status] ?? $invoice->status }}
            </span>
        </div>
    </div>

    <div class="p-6 space-y-4">
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-slate-400 text-xs mb-1">Periode</p>
                <p class="font-medium text-slate-800">{{ $invoice->period_start?->format('d M') }} — {{ $invoice->period_end?->format('d M Y') }}</p>
            </div>
            <div>
                <p class="text-slate-400 text-xs mb-1">Jatuh Tempo</p>
                <p class="font-medium {{ $invoice->status === 'overdue' ? 'text-red-600' : 'text-slate-800' }}">
                    {{ $invoice->due_date?->format('d M Y') }}
                </p>
            </div>
            <div>
                <p class="text-slate-400 text-xs mb-1">Nama Penghuni</p>
                <p class="font-medium text-slate-800">{{ $invoice->tenant?->name }}</p>
            </div>
            <div>
                <p class="text-slate-400 text-xs mb-1">Kamar</p>
                <p class="font-medium text-slate-800">{{ $invoice->tenant?->room?->number ?? '-' }}</p>
            </div>
        </div>

        @if($invoice->notes)
        <div class="pt-4 border-t border-slate-100">
            <p class="text-slate-400 text-xs mb-1">Catatan</p>
            <p class="text-sm text-slate-700">{{ $invoice->notes }}</p>
        </div>
        @endif

        <div class="pt-4 border-t border-slate-100">
            <div class="flex items-center justify-between">
                <p class="font-semibold text-slate-700">Total Tagihan</p>
                <p class="text-2xl font-bold text-slate-900">{{ $invoice->formatted_amount }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Riwayat Pembayaran --}}
@if($invoice->payments->count() > 0)
<div class="bg-white rounded-2xl border border-slate-200">
    <div class="px-6 py-4 border-b border-slate-100">
        <h3 class="font-semibold text-slate-800 text-sm">Riwayat Pembayaran</h3>
    </div>
    <div class="divide-y divide-slate-100">
        @foreach($invoice->payments as $payment)
        <div class="px-6 py-4">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm font-medium text-slate-800">{{ $payment->created_at->format('d M Y, H:i') }}</p>
                @php $ps=['pending'=>'bg-slate-100 text-slate-600','verified'=>'bg-green-100 text-green-700','rejected'=>'bg-red-100 text-red-700']; @endphp
                <span class="text-xs px-2 py-0.5 rounded-full {{ $ps[$payment->status] ?? 'bg-slate-100 text-slate-600' }}">
                    {{ $payment->status === 'verified' ? 'Terverifikasi' : ($payment->status === 'rejected' ? 'Ditolak' : 'Menunggu') }}
                </span>
            </div>
            <p class="text-sm text-slate-600">Jumlah: <span class="font-semibold">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span></p>
            @if($payment->rejection_reason)
            <p class="text-xs text-red-600 mt-1">Alasan ditolak: {{ $payment->rejection_reason }}</p>
            @endif
            @if($payment->proof_file)
            <a href="{{ asset('storage/'.$payment->proof_file) }}" target="_blank"
               class="inline-flex items-center gap-1 text-xs text-blue-600 hover:text-blue-700 mt-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                Lihat Bukti Pembayaran
            </a>
            @endif
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Action Buttons --}}
<div class="flex gap-3">
    @if(in_array($invoice->status, ['unpaid', 'overdue']))
    <a href="{{ route('tenant.payments.upload', $invoice) }}"
       class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 px-4 rounded-xl transition-all text-sm text-center shadow-lg shadow-emerald-600/20">
        💳 Upload Bukti Pembayaran
    </a>
    @endif
    <a href="{{ route('tenant.invoices.index') }}"
       class="px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium rounded-xl transition-colors text-sm text-center">
        ← Kembali
    </a>
</div>
</div>
@endsection
