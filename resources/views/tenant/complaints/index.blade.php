@extends('layouts.tenant')
@section('title', 'Keluhan Saya')
@section('page-title', 'Keluhan Saya')

@section('content')
<div class="space-y-4">

<div class="flex items-center justify-between">
    <p class="text-sm text-slate-500">{{ $complaints->total() }} keluhan tercatat</p>
    <a href="{{ route('tenant.complaints.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-xl transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Buat Keluhan
    </a>
</div>

<div class="bg-white rounded-2xl border border-slate-200 divide-y divide-slate-100">
    @forelse($complaints as $complaint)
    <a href="{{ route('tenant.complaints.show', $complaint) }}" class="flex items-start gap-4 p-5 hover:bg-slate-50 transition-colors">
        @php
        $priorityConfig = ['low'=>'bg-slate-100 text-slate-600','medium'=>'bg-amber-100 text-amber-700','high'=>'bg-red-100 text-red-700'];
        $statusConfig   = ['new'=>'bg-blue-100 text-blue-700','in_progress'=>'bg-amber-100 text-amber-700','resolved'=>'bg-green-100 text-green-700','rejected'=>'bg-red-100 text-red-700'];
        $statusLabel    = ['new'=>'Baru','in_progress'=>'Diproses','resolved'=>'Selesai','rejected'=>'Ditolak'];
        @endphp
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xs px-2 py-0.5 rounded-full {{ $statusConfig[$complaint->status] ?? 'bg-slate-100 text-slate-600' }}">
                    {{ $statusLabel[$complaint->status] ?? $complaint->status }}
                </span>
                <span class="text-xs px-2 py-0.5 rounded-full {{ $priorityConfig[$complaint->priority] ?? '' }}">
                    {{ ucfirst($complaint->priority) }}
                </span>
            </div>
            <p class="text-sm font-semibold text-slate-800">{{ $complaint->title }}</p>
            <p class="text-xs text-slate-400 mt-0.5">{{ $complaint->created_at->diffForHumans() }} • {{ ucfirst($complaint->category) }}</p>
        </div>
        <svg class="w-4 h-4 text-slate-300 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
    </a>
    @empty
    <div class="py-16 text-center text-slate-400">
        <svg class="w-12 h-12 mx-auto mb-3 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
        </svg>
        <p class="text-sm mb-3">Belum ada keluhan</p>
        <a href="{{ route('tenant.complaints.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-sm rounded-xl hover:bg-emerald-700 transition-colors">
            Buat Keluhan Pertama
        </a>
    </div>
    @endforelse
</div>

{{ $complaints->links() }}
</div>
@endsection
