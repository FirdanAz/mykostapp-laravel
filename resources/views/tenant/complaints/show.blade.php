@extends('layouts.tenant')
@section('title', 'Detail Keluhan')
@section('page-title', 'Detail Keluhan')
@section('breadcrumb')
    <span class="mx-1">/</span> <a href="{{ route('tenant.complaints.index') }}" class="hover:text-slate-600">Keluhan</a>
    <span class="mx-1">/</span> Detail
@endsection

@section('content')
<div class="max-w-2xl space-y-5">

{{-- Complaint Detail --}}
<div class="bg-white rounded-2xl border border-slate-200 p-6">
    <div class="flex items-start justify-between gap-4 mb-4">
        <div>
            <h2 class="text-lg font-bold text-slate-800">{{ $complaint->title }}</h2>
            <p class="text-xs text-slate-400 mt-1">{{ $complaint->created_at->format('d M Y, H:i') }}</p>
        </div>
        <div class="flex flex-col gap-1 flex-shrink-0">
            @php
            $sc=['new'=>'bg-blue-100 text-blue-700','in_progress'=>'bg-amber-100 text-amber-700','resolved'=>'bg-green-100 text-green-700','rejected'=>'bg-red-100 text-red-700'];
            $sl=['new'=>'Baru','in_progress'=>'Diproses','resolved'=>'Selesai','rejected'=>'Ditolak'];
            $pc=['low'=>'bg-slate-100 text-slate-600','medium'=>'bg-amber-100 text-amber-700','high'=>'bg-red-100 text-red-700'];
            @endphp
            <span class="text-xs px-2.5 py-1 rounded-full {{ $sc[$complaint->status] ?? '' }} text-center">{{ $sl[$complaint->status] ?? $complaint->status }}</span>
            <span class="text-xs px-2.5 py-1 rounded-full {{ $pc[$complaint->priority] ?? '' }} text-center">{{ ucfirst($complaint->priority) }}</span>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-3 text-sm mb-4">
        <div>
            <p class="text-xs text-slate-400">Kategori</p>
            <p class="font-medium text-slate-700 mt-0.5">{{ ucfirst($complaint->category) }}</p>
        </div>
        @if($complaint->handler)
        <div>
            <p class="text-xs text-slate-400">Ditangani oleh</p>
            <p class="font-medium text-slate-700 mt-0.5">{{ $complaint->handler->name }}</p>
        </div>
        @endif
    </div>

    <div class="bg-slate-50 rounded-xl p-4">
        <p class="text-sm text-slate-700 leading-relaxed">{{ $complaint->description }}</p>
    </div>

    @if($complaint->photos && count($complaint->photos) > 0)
    <div class="flex flex-wrap gap-2 mt-4">
        @foreach($complaint->photos as $photo)
        <a href="{{ asset('storage/'.$photo) }}" target="_blank">
            <img src="{{ asset('storage/'.$photo) }}" alt="" class="w-24 h-24 object-cover rounded-xl border border-slate-200 hover:opacity-90 transition-opacity">
        </a>
        @endforeach
    </div>
    @endif
</div>

{{-- Chat / Replies --}}
<div class="bg-white rounded-2xl border border-slate-200">
    <div class="px-6 py-4 border-b border-slate-100">
        <h3 class="font-semibold text-slate-800 text-sm">Percakapan</h3>
    </div>

    <div class="divide-y divide-slate-50 max-h-96 overflow-y-auto">
        @forelse($complaint->replies as $reply)
        <div class="px-6 py-4 {{ $reply->user_id === auth()->id() ? 'bg-emerald-50/50' : '' }}">
            <div class="flex items-start gap-3">
                <img src="{{ $reply->user->avatar_url }}" alt="" class="w-8 h-8 rounded-full object-cover flex-shrink-0">
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <p class="text-sm font-semibold text-slate-800">{{ $reply->user->name }}</p>
                        @if($reply->user->isAdmin())
                        <span class="text-xs px-1.5 py-0.5 bg-blue-100 text-blue-700 rounded-full">Admin</span>
                        @else
                        <span class="text-xs px-1.5 py-0.5 bg-emerald-100 text-emerald-700 rounded-full">Saya</span>
                        @endif
                    </div>
                    <p class="text-sm text-slate-600 mt-1 leading-relaxed">{{ $reply->message }}</p>
                    <p class="text-xs text-slate-400 mt-1.5">{{ $reply->created_at->diffForHumans() }}</p>
                </div>
            </div>
        </div>
        @empty
        <div class="px-6 py-8 text-center text-slate-400 text-sm">
            Belum ada balasan. Admin akan segera merespon keluhan Anda.
        </div>
        @endforelse
    </div>

    {{-- Reply Form --}}
    @if(!in_array($complaint->status, ['resolved', 'rejected']))
    <div class="px-6 py-4 border-t border-slate-100">
        <form method="POST" action="{{ route('tenant.complaints.reply', $complaint) }}">
            @csrf
            <div class="flex gap-3">
                <textarea name="message" rows="2" required
                          class="flex-1 px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 resize-none"
                          placeholder="Tulis balasan atau info tambahan..."></textarea>
                <button type="submit"
                        class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-xl transition-colors text-sm flex-shrink-0 self-end">
                    Kirim
                </button>
            </div>
        </form>
    </div>
    @else
    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 rounded-b-2xl">
        <p class="text-xs text-slate-400 text-center">
            Keluhan ini sudah {{ $sl[$complaint->status] }} — percakapan ditutup.
        </p>
    </div>
    @endif
</div>

<a href="{{ route('tenant.complaints.index') }}"
   class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-slate-700 transition-colors">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
    </svg>
    Kembali ke daftar keluhan
</a>
</div>
@endsection
