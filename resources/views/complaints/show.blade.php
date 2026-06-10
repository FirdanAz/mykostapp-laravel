@extends('layouts.app')
@section('title',$complaint->title)
@section('page-title','Detail Keluhan')
@section('breadcrumb') <span class="mx-1">/</span> <a href="{{ route('admin.complaints.index') }}" class="hover:text-slate-600">Keluhan</a> <span class="mx-1">/</span> Detail @endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Main Content --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Complaint Detail --}}
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
            @php
            $ss = ['new'=>'bg-blue-100 text-blue-700','in_progress'=>'bg-amber-100 text-amber-700','resolved'=>'bg-green-100 text-green-700','rejected'=>'bg-red-100 text-red-700'];
            $ps = ['low'=>'bg-green-100 text-green-700','medium'=>'bg-amber-100 text-amber-700','high'=>'bg-red-100 text-red-700'];
            @endphp
            <div class="p-6 border-b border-slate-100">
                <div class="flex items-start justify-between gap-3 flex-wrap mb-4">
                    <h2 class="font-bold text-slate-900 text-lg">{{ $complaint->title }}</h2>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $ps[$complaint->priority] ?? '' }}">{{ $complaint->priority_label }}</span>
                        <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $ss[$complaint->status] ?? '' }}">{{ $complaint->status_label }}</span>
                    </div>
                </div>
                <div class="flex items-center gap-3 mb-4">
                    <img src="{{ $complaint->tenant->photo_url }}" alt="" class="w-10 h-10 rounded-full object-cover">
                    <div>
                        <p class="font-semibold text-slate-800 text-sm">{{ $complaint->tenant->name }}</p>
                        <p class="text-xs text-slate-400">Kamar {{ $complaint->tenant->room->number }} • {{ $complaint->created_at->format('d M Y H:i') }}</p>
                    </div>
                    <span class="ml-auto text-xs bg-slate-100 text-slate-600 px-2.5 py-1 rounded-lg">{{ $complaint->category_label }}</span>
                </div>
                <p class="text-slate-700 leading-relaxed">{{ $complaint->description }}</p>
            </div>

            @if($complaint->photos && count($complaint->photos))
            <div class="p-6 border-b border-slate-100">
                <p class="text-sm font-medium text-slate-600 mb-3">Foto Pendukung</p>
                <div class="grid grid-cols-3 gap-3">
                    @foreach($complaint->photos as $photo)
                    <a href="{{ asset('storage/'.$photo) }}" target="_blank">
                        <img src="{{ asset('storage/'.$photo) }}" alt="" class="w-full h-28 rounded-xl object-cover hover:opacity-80 transition-opacity">
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Replies --}}
        <div class="bg-white rounded-2xl border border-slate-200">
            <div class="px-6 py-4 border-b border-slate-100">
                <h3 class="font-semibold text-slate-800 text-sm">Diskusi ({{ $complaint->replies->count() }})</h3>
            </div>

            @if($complaint->replies->count())
            <div class="divide-y divide-slate-100">
                @foreach($complaint->replies as $reply)
                <div class="p-5 flex gap-3">
                    <img src="{{ $reply->user->avatar_url }}" alt="" class="w-8 h-8 rounded-full object-cover flex-shrink-0 mt-0.5">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1.5">
                            <span class="font-semibold text-slate-800 text-sm">{{ $reply->user->name }}</span>
                            @if($reply->user->isAdmin())
                            <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-medium">Admin</span>
                            @endif
                            <span class="text-xs text-slate-400 ml-auto">{{ $reply->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-sm text-slate-700 leading-relaxed">{{ $reply->message }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="p-8 text-center text-slate-400 text-sm">Belum ada balasan</div>
            @endif

            {{-- Reply Form --}}
            @if(!in_array($complaint->status, ['resolved','rejected']))
            <div class="p-5 bg-slate-50 border-t border-slate-100">
                <form method="POST" action="{{ route('admin.complaints.reply', $complaint) }}" class="flex gap-3">
                    @csrf
                    <img src="{{ auth()->user()->avatar_url }}" alt="" class="w-8 h-8 rounded-full object-cover flex-shrink-0 mt-1">
                    <div class="flex-1">
                        <textarea name="message" rows="3" required
                                  class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none @error('message') border-red-400 @enderror"
                                  placeholder="Tulis balasan..."></textarea>
                        @error('message') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        <div class="flex justify-end mt-2">
                            <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-colors">
                                Kirim Balasan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            @endif
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="space-y-4">
        {{-- Update Status --}}
        @if(!in_array($complaint->status, ['resolved','rejected']))
        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <h3 class="font-semibold text-slate-800 text-sm mb-4">Update Status</h3>
            <form method="POST" action="{{ route('admin.complaints.update-status', $complaint) }}" class="space-y-3">
                @csrf @method('PATCH')
                <select name="status" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="new"         {{ $complaint->status=='new'         ? 'selected':'' }}>Baru</option>
                    <option value="in_progress" {{ $complaint->status=='in_progress' ? 'selected':'' }}>Diproses</option>
                    <option value="resolved"    {{ $complaint->status=='resolved'    ? 'selected':'' }}>Selesai</option>
                    <option value="rejected"    {{ $complaint->status=='rejected'    ? 'selected':'' }}>Ditolak</option>
                </select>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-xl text-sm transition-colors">
                    Update Status
                </button>
            </form>
        </div>
        @endif

        {{-- Info --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5 space-y-3">
            <h3 class="font-semibold text-slate-800 text-sm mb-1">Informasi</h3>
            <div>
                <p class="text-xs text-slate-400">Dilaporkan</p>
                <p class="text-sm font-medium text-slate-700">{{ $complaint->created_at->format('d M Y H:i') }}</p>
            </div>
            @if($complaint->handler)
            <div>
                <p class="text-xs text-slate-400">Ditangani oleh</p>
                <div class="flex items-center gap-2 mt-1">
                    <img src="{{ $complaint->handler->avatar_url }}" alt="" class="w-6 h-6 rounded-full object-cover">
                    <p class="text-sm font-medium text-slate-700">{{ $complaint->handler->name }}</p>
                </div>
            </div>
            @endif
            @if($complaint->resolved_at)
            <div>
                <p class="text-xs text-slate-400">Diselesaikan</p>
                <p class="text-sm font-medium text-slate-700">{{ $complaint->resolved_at->format('d M Y H:i') }}</p>
            </div>
            @endif
        </div>

        <a href="{{ route('admin.tenants.show', $complaint->tenant) }}"
           class="w-full flex items-center gap-3 bg-white border border-slate-200 rounded-2xl p-4 hover:bg-slate-50 transition-colors">
            <img src="{{ $complaint->tenant->photo_url }}" alt="" class="w-10 h-10 rounded-full object-cover">
            <div>
                <p class="font-semibold text-slate-800 text-sm">{{ $complaint->tenant->name }}</p>
                <p class="text-xs text-blue-600 mt-0.5">Lihat profil →</p>
            </div>
        </a>
    </div>
</div>
@endsection
