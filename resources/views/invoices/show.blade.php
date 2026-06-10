@extends('layouts.app')
@section('title',$invoice->invoice_number)
@section('page-title','Detail Tagihan')
@section('breadcrumb') <span class="mx-1">/</span> <a href="{{ route('invoices.index') }}" class="hover:text-slate-600">Tagihan</a> <span class="mx-1">/</span> {{ $invoice->invoice_number }} @endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Invoice Card --}}
    <div class="lg:col-span-2 space-y-5">
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
            {{-- Invoice Header --}}
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-6 text-white">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-blue-200 text-xs font-medium uppercase tracking-wide">Invoice</p>
                        <p class="text-2xl font-bold mt-1">{{ $invoice->invoice_number }}</p>
                    </div>
                    @php
                    $ss = ['unpaid'=>'bg-amber-400 text-amber-900','pending_verification'=>'bg-blue-300 text-blue-900','paid'=>'bg-green-400 text-green-900','rejected'=>'bg-red-400 text-red-900','overdue'=>'bg-orange-400 text-orange-900'];
                    @endphp
                    <span class="text-sm font-bold px-3 py-1.5 rounded-xl {{ $ss[$invoice->status] ?? 'bg-white/20 text-white' }}">
                        {{ $invoice->status_label }}
                    </span>
                </div>
                <div class="mt-6 grid grid-cols-3 gap-4">
                    <div>
                        <p class="text-blue-200 text-xs">Nominal</p>
                        <p class="font-bold text-lg mt-0.5">{{ $invoice->formatted_amount }}</p>
                    </div>
                    <div>
                        <p class="text-blue-200 text-xs">Periode</p>
                        <p class="font-semibold mt-0.5">{{ $invoice->period_start->translatedFormat('M Y') }}</p>
                    </div>
                    <div>
                        <p class="text-blue-200 text-xs">Jatuh Tempo</p>
                        <p class="font-semibold mt-0.5 {{ $invoice->due_date->isPast() && $invoice->status !== 'paid' ? 'text-red-300' : '' }}">
                            {{ $invoice->due_date->format('d M Y') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Tenant Info --}}
            <div class="p-6 border-b border-slate-100">
                <div class="flex items-center gap-4">
                    <img src="{{ $invoice->tenant->photo_url }}" alt="" class="w-14 h-14 rounded-2xl object-cover ring-2 ring-slate-100">
                    <div>
                        <p class="font-bold text-slate-900">{{ $invoice->tenant->name }}</p>
                        <p class="text-sm text-slate-500">{{ $invoice->tenant->phone }}</p>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-lg font-medium">Kamar {{ $invoice->tenant->room->number }}</span>
                            <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded-lg">Lantai {{ $invoice->tenant->room->floor }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Details --}}
            <div class="p-6 space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500">Sewa Kamar</span>
                    <span class="font-medium text-slate-800">{{ $invoice->formatted_amount }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500">Periode</span>
                    <span class="font-medium text-slate-800">{{ $invoice->period_start->format('d M') }} – {{ $invoice->period_end->format('d M Y') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500">Dibuat</span>
                    <span class="font-medium text-slate-800">{{ $invoice->created_at->format('d M Y H:i') }}</span>
                </div>
                @if($invoice->notes)
                <div class="pt-3 border-t border-slate-100">
                    <p class="text-xs text-slate-500 mb-1">Catatan</p>
                    <p class="text-sm text-slate-700">{{ $invoice->notes }}</p>
                </div>
                @endif
                <div class="pt-3 border-t border-slate-200 flex justify-between">
                    <span class="font-bold text-slate-800">Total</span>
                    <span class="font-bold text-lg text-blue-600">{{ $invoice->formatted_amount }}</span>
                </div>
            </div>
        </div>

        {{-- Payment History --}}
        @if($invoice->payments->count())
        <div class="bg-white rounded-2xl border border-slate-200">
            <div class="px-6 py-4 border-b border-slate-100">
                <h3 class="font-semibold text-slate-800 text-sm">Riwayat Pembayaran</h3>
            </div>
            @foreach($invoice->payments as $payment)
            @php $ps = ['pending'=>'bg-amber-100 text-amber-700','verified'=>'bg-green-100 text-green-700','rejected'=>'bg-red-100 text-red-700']; @endphp
            <div class="p-6 border-b border-slate-50 last:border-0">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <a href="{{ $payment->proof_url }}" target="_blank"
                           class="w-16 h-16 rounded-xl overflow-hidden border border-slate-200 flex-shrink-0 hover:opacity-80 transition-opacity">
                            <img src="{{ $payment->proof_url }}" alt="" class="w-full h-full object-cover" onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%236b7280%22 stroke-width=%221.5%22><path stroke-linecap=%22round%22 stroke-linejoin=%22round%22 d=%22M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z%22/></svg>'">
                        </a>
                        <div>
                            <p class="font-semibold text-slate-800">{{ $payment->formatted_amount }}</p>
                            <p class="text-xs text-slate-400 mt-0.5">Dikirim: {{ $payment->created_at->format('d M Y H:i') }}</p>
                            @if($payment->verified_at)
                            <p class="text-xs text-slate-400">Diproses: {{ $payment->verified_at->format('d M Y H:i') }} oleh {{ $payment->verifiedBy?->name }}</p>
                            @endif
                            @if($payment->rejection_reason)
                            <p class="text-xs text-red-600 mt-1">Alasan ditolak: {{ $payment->rejection_reason }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex flex-col items-end gap-2">
                        <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $ps[$payment->status] ?? 'bg-slate-100 text-slate-600' }}">
                            {{ $payment->status_label }}
                        </span>
                        @if($payment->status === 'pending')
                        <a href="{{ route('payments.show', $payment) }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">Verifikasi →</a>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Sidebar --}}
    <div class="space-y-4">
        <div class="bg-white rounded-2xl border border-slate-200 p-5 space-y-3">
            <h3 class="font-semibold text-slate-800 text-sm">Aksi</h3>

            @if(in_array($invoice->status, ['unpaid','overdue','rejected']))
            <a href="{{ route('payments.upload', $invoice) }}"
               class="w-full flex items-center justify-center gap-2 bg-green-600 text-white text-sm font-semibold py-2.5 rounded-xl hover:bg-green-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Upload Bukti Bayar
            </a>
            @endif

            @if($invoice->status === 'pending_verification' && $invoice->latestPayment)
            <a href="{{ route('payments.show', $invoice->latestPayment) }}"
               class="w-full flex items-center justify-center gap-2 bg-blue-600 text-white text-sm font-semibold py-2.5 rounded-xl hover:bg-blue-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Verifikasi Pembayaran
            </a>
            @endif

            <a href="{{ route('tenants.show', $invoice->tenant) }}"
               class="w-full flex items-center justify-center gap-2 bg-slate-100 text-slate-700 text-sm font-semibold py-2.5 rounded-xl hover:bg-slate-200 transition-colors">
                Lihat Profil Penghuni
            </a>

            @if($invoice->status !== 'paid')
            <form method="POST" action="{{ route('invoices.destroy', $invoice) }}" onsubmit="return confirm('Hapus invoice ini?')">
                @csrf @method('DELETE')
                <button type="submit" class="w-full flex items-center justify-center gap-2 bg-red-50 text-red-600 text-sm font-semibold py-2.5 rounded-xl hover:bg-red-100 transition-colors">
                    Hapus Invoice
                </button>
            </form>
            @endif
        </div>

        {{-- Status timeline --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <h3 class="font-semibold text-slate-800 text-sm mb-4">Status Timeline</h3>
            @php
            $steps = [
                ['key'=>'created',  'label'=>'Invoice Dibuat',      'done'=>true,                                    'date'=>$invoice->created_at->format('d M Y')],
                ['key'=>'uploaded', 'label'=>'Bukti Diunggah',       'done'=>$invoice->payments->count() > 0,        'date'=>$invoice->latestPayment?->created_at?->format('d M Y') ?? ''],
                ['key'=>'verified', 'label'=>'Diverifikasi / Ditolak','done'=>in_array($invoice->status,['paid','rejected']), 'date'=>$invoice->latestPayment?->verified_at?->format('d M Y') ?? ''],
                ['key'=>'done',     'label'=>'Selesai',              'done'=>$invoice->status === 'paid',            'date'=>''],
            ];
            @endphp
            <div class="space-y-3">
                @foreach($steps as $i => $step)
                <div class="flex gap-3">
                    <div class="flex flex-col items-center">
                        <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 {{ $step['done'] ? 'bg-blue-600' : 'bg-slate-200' }}">
                            @if($step['done'])
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            @else
                            <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                            @endif
                        </div>
                        @if(!$loop->last)
                        <div class="w-0.5 h-6 {{ $step['done'] ? 'bg-blue-300' : 'bg-slate-200' }} mt-1"></div>
                        @endif
                    </div>
                    <div class="pb-3">
                        <p class="text-sm font-medium {{ $step['done'] ? 'text-slate-800' : 'text-slate-400' }}">{{ $step['label'] }}</p>
                        @if($step['date'])
                        <p class="text-xs text-slate-400 mt-0.5">{{ $step['date'] }}</p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
