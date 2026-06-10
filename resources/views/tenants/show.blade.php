@extends('layouts.app')
@section('title',$tenant->name)
@section('page-title','Detail Penghuni')
@section('breadcrumb') <span class="mx-1">/</span> <a href="{{ route('tenants.index') }}" class="hover:text-slate-600">Penghuni</a> <span class="mx-1">/</span> {{ $tenant->name }} @endsection
@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Left --}}
    <div class="space-y-5">
        <div class="bg-white rounded-2xl border border-slate-200 p-6 text-center">
            <img src="{{ $tenant->photo_url }}" alt="" class="w-24 h-24 rounded-2xl object-cover mx-auto ring-4 ring-slate-100 mb-4">
            <h2 class="font-bold text-slate-900 text-lg">{{ $tenant->name }}</h2>
            <p class="text-slate-500 text-sm">{{ $tenant->gender_label }}</p>
            <span class="inline-flex items-center gap-1.5 mt-2 text-xs font-medium px-3 py-1 rounded-full {{ $tenant->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ $tenant->status === 'active' ? 'bg-green-500' : 'bg-slate-400' }}"></span>
                {{ $tenant->status_label }}
            </span>
            <div class="mt-4 p-3 bg-blue-50 rounded-xl">
                <p class="text-xs text-blue-600 font-medium">Kamar</p>
                <p class="text-xl font-bold text-blue-800 mt-0.5">{{ $tenant->room->number }}</p>
                <p class="text-xs text-blue-500">Lantai {{ $tenant->room->floor }} — {{ $tenant->room->formatted_price }}/bln</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 space-y-3">
            @if($tenant->phone)
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                </div>
                <div><p class="text-xs text-slate-400">Nomor HP</p><p class="text-sm font-medium text-slate-700">{{ $tenant->phone }}</p></div>
            </div>
            @endif
            @if($tenant->email)
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <div><p class="text-xs text-slate-400">Email</p><p class="text-sm font-medium text-slate-700">{{ $tenant->email }}</p></div>
            </div>
            @endif
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div><p class="text-xs text-slate-400">Tanggal Masuk</p><p class="text-sm font-medium text-slate-700">{{ $tenant->start_date->format('d M Y') }}</p></div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 space-y-2">
            <a href="{{ route('tenants.edit', $tenant) }}" class="w-full flex items-center justify-center gap-2 bg-blue-600 text-white text-sm font-semibold py-2.5 rounded-xl hover:bg-blue-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit Data
            </a>
            <a href="{{ route('invoices.create', ['tenant_id' => $tenant->id]) }}" class="w-full flex items-center justify-center gap-2 bg-green-600 text-white text-sm font-semibold py-2.5 rounded-xl hover:bg-green-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Buat Tagihan
            </a>
        </div>
    </div>

    {{-- Right --}}
    <div class="lg:col-span-2 space-y-5">
        {{-- Invoices --}}
        <div class="bg-white rounded-2xl border border-slate-200">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <h3 class="font-semibold text-slate-800 text-sm">Riwayat Tagihan</h3>
                <a href="{{ route('invoices.create', ['tenant_id'=>$tenant->id]) }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">+ Buat Tagihan</a>
            </div>
            @forelse($tenant->invoices->take(5) as $invoice)
            <a href="{{ route('invoices.show', $invoice) }}" class="flex items-center gap-4 px-6 py-3.5 border-b border-slate-50 hover:bg-slate-50 transition-colors">
                <div class="flex-1">
                    <p class="text-sm font-medium text-slate-800">{{ $invoice->invoice_number }}</p>
                    <p class="text-xs text-slate-400">{{ $invoice->period_start->format('M Y') }} • Jatuh tempo: {{ $invoice->due_date->format('d M Y') }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-semibold text-slate-800">{{ $invoice->formatted_amount }}</p>
                    @php $sc=['unpaid'=>'bg-amber-100 text-amber-700','pending_verification'=>'bg-blue-100 text-blue-700','paid'=>'bg-green-100 text-green-700','rejected'=>'bg-red-100 text-red-700','overdue'=>'bg-orange-100 text-orange-700']; @endphp
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $sc[$invoice->status] ?? 'bg-slate-100 text-slate-600' }}">{{ $invoice->status_label }}</span>
                </div>
            </a>
            @empty
            <div class="px-6 py-8 text-center text-slate-400 text-sm">Belum ada tagihan</div>
            @endforelse
        </div>

        {{-- Complaints --}}
        <div class="bg-white rounded-2xl border border-slate-200">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <h3 class="font-semibold text-slate-800 text-sm">Riwayat Keluhan</h3>
                <a href="{{ route('complaints.create', ['tenant_id'=>$tenant->id]) }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">+ Catat Keluhan</a>
            </div>
            @forelse($tenant->complaints->take(5) as $c)
            <a href="{{ route('complaints.show', $c) }}" class="flex items-center gap-4 px-6 py-3.5 border-b border-slate-50 hover:bg-slate-50 transition-colors">
                <div class="flex-1">
                    <p class="text-sm font-medium text-slate-800">{{ $c->title }}</p>
                    <p class="text-xs text-slate-400">{{ $c->category_label }} • {{ $c->created_at->diffForHumans() }}</p>
                </div>
                @php $sc2=['new'=>'bg-blue-100 text-blue-700','in_progress'=>'bg-amber-100 text-amber-700','resolved'=>'bg-green-100 text-green-700','rejected'=>'bg-red-100 text-red-700']; @endphp
                <span class="text-xs px-2.5 py-1 rounded-full {{ $sc2[$c->status] ?? 'bg-slate-100 text-slate-600' }}">{{ $c->status_label }}</span>
            </a>
            @empty
            <div class="px-6 py-8 text-center text-slate-400 text-sm">Belum ada keluhan</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
