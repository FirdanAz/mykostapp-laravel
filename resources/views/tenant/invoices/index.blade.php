@extends('layouts.tenant')
@section('title', 'Tagihan Saya')
@section('page-title', 'Tagihan Saya')
@section('breadcrumb') <span class="mx-1">/</span> Tagihan @endsection

@section('content')
<div class="space-y-4">

<div class="bg-white rounded-2xl border border-slate-200 divide-y divide-slate-100">
    @forelse($invoices as $invoice)
    <div class="p-5 flex items-center gap-4">
        {{-- Status Icon --}}
        @php
        $statusConfig = [
            'unpaid'               => ['bg'=>'bg-amber-100', 'text'=>'text-amber-700', 'label'=>'Belum Bayar', 'dot'=>'bg-amber-400'],
            'overdue'              => ['bg'=>'bg-red-100',   'text'=>'text-red-700',   'label'=>'Terlambat',   'dot'=>'bg-red-500'],
            'pending_verification' => ['bg'=>'bg-blue-100',  'text'=>'text-blue-700',  'label'=>'Diverifikasi','dot'=>'bg-blue-400'],
            'paid'                 => ['bg'=>'bg-green-100', 'text'=>'text-green-700', 'label'=>'Lunas',       'dot'=>'bg-green-500'],
        ];
        $cfg = $statusConfig[$invoice->status] ?? ['bg'=>'bg-slate-100','text'=>'text-slate-600','label'=>$invoice->status,'dot'=>'bg-slate-400'];
        @endphp

        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 mb-1">
                <span class="w-2 h-2 rounded-full {{ $cfg['dot'] }} flex-shrink-0"></span>
                <p class="text-sm font-semibold text-slate-800">{{ $invoice->invoice_number }}</p>
                <span class="text-xs px-2 py-0.5 rounded-full {{ $cfg['bg'] }} {{ $cfg['text'] }}">{{ $cfg['label'] }}</span>
            </div>
            <p class="text-xs text-slate-500">
                Periode: {{ $invoice->period_start?->format('d M') }} — {{ $invoice->period_end?->format('d M Y') }}
            </p>
            <p class="text-xs text-slate-400 mt-0.5">
                Jatuh tempo: {{ $invoice->due_date?->format('d M Y') }}
            </p>
        </div>

        <div class="text-right flex-shrink-0">
            <p class="text-lg font-bold text-slate-900">{{ $invoice->formatted_amount }}</p>
        </div>

        <div class="flex-shrink-0 flex flex-col gap-2">
            <a href="{{ route('tenant.invoices.show', $invoice) }}"
               class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-medium rounded-lg transition-colors text-center">
                Detail
            </a>
            @if(in_array($invoice->status, ['unpaid', 'overdue']))
            <a href="{{ route('tenant.payments.upload', $invoice) }}"
               class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-medium rounded-lg transition-colors text-center">
                Bayar
            </a>
            @endif
        </div>
    </div>
    @empty
    <div class="py-16 text-center text-slate-400">
        <svg class="w-12 h-12 mx-auto mb-3 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <p class="text-sm">Belum ada tagihan</p>
    </div>
    @endforelse
</div>

{{ $invoices->links() }}
</div>
@endsection
