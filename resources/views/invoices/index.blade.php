@extends('layouts.app')
@section('title','Tagihan')
@section('page-title','Manajemen Tagihan')
@section('breadcrumb') <span class="mx-1">/</span> Tagihan @endsection

@section('content')
<div class="space-y-5">

{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div class="flex gap-3">
        <a href="{{ route('admin.invoices.create') }}"
           class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-all shadow-lg shadow-blue-600/20">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Buat Tagihan
        </a>
        <button onclick="document.getElementById('bulk-modal').classList.remove('hidden')"
                class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-all shadow-lg shadow-emerald-600/20">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            Generate Bulanan
        </button>
    </div>
</div>

{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    @php
    $statCards = [
        ['label'=>'Total Invoice',    'value'=>$stats['total'],   'color'=>'slate',  'icon'=>'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        ['label'=>'Belum Dibayar',    'value'=>$stats['unpaid'],  'color'=>'amber',  'icon'=>'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
        ['label'=>'Menunggu Verifikasi','value'=>$stats['pending'],'color'=>'blue',  'icon'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['label'=>'Lunas',            'value'=>$stats['paid'],    'color'=>'green',  'icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
    ];
    $colors = [
        'slate' => ['bg'=>'bg-slate-100', 'text'=>'text-slate-600', 'icon'=>'bg-slate-200'],
        'amber' => ['bg'=>'bg-amber-50',  'text'=>'text-amber-600', 'icon'=>'bg-amber-100'],
        'blue'  => ['bg'=>'bg-blue-50',   'text'=>'text-blue-600',  'icon'=>'bg-blue-100'],
        'green' => ['bg'=>'bg-green-50',  'text'=>'text-green-600', 'icon'=>'bg-green-100'],
    ];
    @endphp
    @foreach($statCards as $sc)
    @php $c = $colors[$sc['color']]; @endphp
    <div class="bg-white rounded-2xl border border-slate-200 p-5">
        <div class="{{ $c['icon'] }} w-9 h-9 rounded-xl flex items-center justify-center mb-3">
            <svg class="w-5 h-5 {{ $c['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sc['icon'] }}"/>
            </svg>
        </div>
        <p class="text-2xl font-bold text-slate-900">{{ $sc['value'] }}</p>
        <p class="text-sm text-slate-500 mt-0.5">{{ $sc['label'] }}</p>
    </div>
    @endforeach
</div>

{{-- Filters --}}
<div class="bg-white rounded-2xl border border-slate-200 p-4">
    <form method="GET" class="flex flex-wrap gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari invoice / penghuni..."
               class="flex-1 min-w-48 px-4 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        <select name="status" class="px-4 py-2 rounded-xl border border-slate-200 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Semua Status</option>
            <option value="unpaid"               {{ request('status')=='unpaid'               ? 'selected':'' }}>Belum Dibayar</option>
            <option value="pending_verification" {{ request('status')=='pending_verification' ? 'selected':'' }}>Menunggu Verifikasi</option>
            <option value="paid"                 {{ request('status')=='paid'                 ? 'selected':'' }}>Lunas</option>
            <option value="rejected"             {{ request('status')=='rejected'             ? 'selected':'' }}>Ditolak</option>
            <option value="overdue"              {{ request('status')=='overdue'              ? 'selected':'' }}>Terlambat</option>
        </select>
        <select name="month" class="px-4 py-2 rounded-xl border border-slate-200 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Semua Bulan</option>
            @for($m = 1; $m <= 12; $m++)
            <option value="{{ $m }}" {{ request('month') == $m ? 'selected':'' }}>{{ \Carbon\Carbon::create(null,$m)->translatedFormat('F') }}</option>
            @endfor
        </select>
        <select name="year" class="px-4 py-2 rounded-xl border border-slate-200 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
            @for($y = date('Y'); $y >= date('Y')-3; $y--)
            <option value="{{ $y }}" {{ request('year', date('Y')) == $y ? 'selected':'' }}>{{ $y }}</option>
            @endfor
        </select>
        <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-sm font-medium rounded-xl hover:bg-slate-700">Filter</button>
        @if(request()->hasAny(['search','status','month','year']))
        <a href="{{ route('admin.invoices.index') }}" class="px-4 py-2 bg-slate-100 text-slate-600 text-sm font-medium rounded-xl hover:bg-slate-200">Reset</a>
        @endif
    </form>
</div>

{{-- Table --}}
<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
    @if($invoices->isEmpty())
    <div class="p-16 text-center">
        <svg class="w-12 h-12 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <p class="font-semibold text-slate-600">Belum ada tagihan</p>
        <p class="text-sm text-slate-400 mt-1">Buat tagihan manual atau generate otomatis per bulan</p>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200">
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Invoice</th>
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Penghuni</th>
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide hidden md:table-cell">Periode</th>
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Nominal</th>
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide hidden lg:table-cell">Jatuh Tempo</th>
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Status</th>
                    <th class="px-6 py-3.5"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($invoices as $invoice)
                @php
                $statusStyle = [
                    'unpaid'               => 'bg-amber-100 text-amber-700',
                    'pending_verification' => 'bg-blue-100 text-blue-700',
                    'paid'                 => 'bg-green-100 text-green-700',
                    'rejected'             => 'bg-red-100 text-red-700',
                    'overdue'              => 'bg-orange-100 text-orange-700',
                ];
                @endphp
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">
                        <p class="font-mono text-xs font-semibold text-slate-700">{{ $invoice->invoice_number }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $invoice->created_at->format('d M Y') }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <img src="{{ $invoice->tenant->photo_url }}" alt="" class="w-7 h-7 rounded-full object-cover flex-shrink-0">
                            <div>
                                <p class="font-medium text-slate-800 text-sm">{{ $invoice->tenant->name }}</p>
                                <p class="text-xs text-slate-400">Kamar {{ $invoice->tenant->room->number }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 hidden md:table-cell">
                        <p class="text-slate-600 text-xs">{{ $invoice->period_start->translatedFormat('M Y') }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-semibold text-slate-800">{{ $invoice->formatted_amount }}</p>
                    </td>
                    <td class="px-6 py-4 hidden lg:table-cell">
                        <p class="text-slate-600 text-sm {{ $invoice->due_date->isPast() && $invoice->status !== 'paid' ? 'text-red-600 font-medium' : '' }}">
                            {{ $invoice->due_date->format('d M Y') }}
                        </p>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $statusStyle[$invoice->status] ?? 'bg-slate-100 text-slate-600' }}">
                            {{ $invoice->status_label }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-1.5 justify-end">
                            <a href="{{ route('admin.invoices.show', $invoice) }}"
                               class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                            @if(in_array($invoice->status, ['unpaid','overdue','rejected']))
                            <a href="{{ route('admin.payments.upload', $invoice) }}"
                               class="p-1.5 text-slate-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors" title="Upload Bukti">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                            </a>
                            @endif
                            @if($invoice->status !== 'paid')
                            <form method="POST" action="{{ route('admin.invoices.destroy', $invoice) }}" onsubmit="return confirm('Hapus invoice ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-slate-100">{{ $invoices->links() }}</div>
    @endif
</div>

</div>

{{-- Generate Bulk Modal --}}
<div id="bulk-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-bold text-slate-800">Generate Tagihan Bulanan</h3>
            <button onclick="document.getElementById('bulk-modal').classList.add('hidden')" class="p-1.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <p class="text-sm text-slate-500 mb-5">Buat tagihan otomatis untuk semua penghuni aktif sesuai bulan dan tahun yang dipilih.</p>
        <form method="POST" action="{{ route('admin.invoices.generate-bulk') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Bulan</label>
                    <select name="month" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @for($m=1; $m<=12; $m++)
                        <option value="{{ $m }}" {{ date('n') == $m ? 'selected':'' }}>
                            {{ \Carbon\Carbon::create(null,$m)->translatedFormat('F') }}
                        </option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Tahun</label>
                    <select name="year" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @for($y=date('Y'); $y>=date('Y')-2; $y--)
                        <option value="{{ $y }}" {{ date('Y') == $y ? 'selected':'' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2.5 rounded-xl text-sm transition-colors">
                    Generate Tagihan
                </button>
                <button type="button" onclick="document.getElementById('bulk-modal').classList.add('hidden')"
                        class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold py-2.5 rounded-xl text-sm transition-colors">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
