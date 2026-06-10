@extends('layouts.app')
@section('title','Pembayaran')
@section('page-title','Manajemen Pembayaran')
@section('breadcrumb') <span class="mx-1">/</span> Pembayaran @endsection

@section('content')
<div class="space-y-5">

<div class="bg-white rounded-2xl border border-slate-200 p-4">
    <form method="GET" class="flex flex-wrap gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari invoice / penghuni..."
               class="flex-1 min-w-48 px-4 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        <select name="status" class="px-4 py-2 rounded-xl border border-slate-200 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Semua Status</option>
            <option value="pending"  {{ request('status')=='pending'  ? 'selected':'' }}>Menunggu Verifikasi</option>
            <option value="verified" {{ request('status')=='verified' ? 'selected':'' }}>Terverifikasi</option>
            <option value="rejected" {{ request('status')=='rejected' ? 'selected':'' }}>Ditolak</option>
        </select>
        <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-sm font-medium rounded-xl hover:bg-slate-700">Filter</button>
        @if(request()->hasAny(['search','status']))
        <a href="{{ route('admin.payments.index') }}" class="px-4 py-2 bg-slate-100 text-slate-600 text-sm font-medium rounded-xl hover:bg-slate-200">Reset</a>
        @endif
    </form>
</div>

<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
    @if($payments->isEmpty())
    <div class="p-16 text-center">
        <svg class="w-12 h-12 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
        </svg>
        <p class="font-semibold text-slate-600">Belum ada data pembayaran</p>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200">
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Invoice</th>
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Penghuni</th>
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide hidden md:table-cell">Bukti</th>
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Nominal</th>
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide hidden lg:table-cell">Tanggal</th>
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Status</th>
                    <th class="px-6 py-3.5"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($payments as $payment)
                @php $ps = ['pending'=>'bg-amber-100 text-amber-700','verified'=>'bg-green-100 text-green-700','rejected'=>'bg-red-100 text-red-700']; @endphp
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.invoices.show', $payment->invoice) }}" class="font-mono text-xs font-semibold text-blue-600 hover:text-blue-800">
                            {{ $payment->invoice->invoice_number }}
                        </a>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <img src="{{ $payment->invoice->tenant->photo_url }}" alt="" class="w-7 h-7 rounded-full object-cover flex-shrink-0">
                            <div>
                                <p class="font-medium text-slate-800 text-sm">{{ $payment->invoice->tenant->name }}</p>
                                <p class="text-xs text-slate-400">Kamar {{ $payment->invoice->tenant->room->number }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 hidden md:table-cell">
                        <a href="{{ $payment->proof_url }}" target="_blank"
                           class="w-12 h-12 rounded-xl overflow-hidden block border border-slate-200 hover:border-blue-400 transition-colors">
                            <img src="{{ $payment->proof_url }}" alt="" class="w-full h-full object-cover">
                        </a>
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-semibold text-slate-800">{{ $payment->formatted_amount }}</p>
                    </td>
                    <td class="px-6 py-4 text-slate-500 text-xs hidden lg:table-cell">
                        {{ $payment->created_at->format('d M Y H:i') }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $ps[$payment->status] ?? 'bg-slate-100 text-slate-600' }}">
                            {{ $payment->status_label }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.payments.show', $payment) }}"
                           class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors inline-flex">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-slate-100">{{ $payments->links() }}</div>
    @endif
</div>

</div>
@endsection
