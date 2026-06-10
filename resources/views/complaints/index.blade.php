@extends('layouts.app')
@section('title','Keluhan')
@section('page-title','Manajemen Keluhan')
@section('breadcrumb') <span class="mx-1">/</span> Keluhan @endsection

@section('content')
<div class="space-y-5">

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <p class="text-sm text-slate-500">Total <span class="font-semibold text-slate-700">{{ $complaints->total() }}</span> keluhan</p>
    <a href="{{ route('admin.complaints.create') }}"
       class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-all shadow-lg shadow-blue-600/20">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Catat Keluhan
    </a>
</div>

<div class="bg-white rounded-2xl border border-slate-200 p-4">
    <form method="GET" class="flex flex-wrap gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul / penghuni..."
               class="flex-1 min-w-48 px-4 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        <select name="status" class="px-4 py-2 rounded-xl border border-slate-200 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Semua Status</option>
            <option value="new"         {{ request('status')=='new'         ? 'selected':'' }}>Baru</option>
            <option value="in_progress" {{ request('status')=='in_progress' ? 'selected':'' }}>Diproses</option>
            <option value="resolved"    {{ request('status')=='resolved'    ? 'selected':'' }}>Selesai</option>
            <option value="rejected"    {{ request('status')=='rejected'    ? 'selected':'' }}>Ditolak</option>
        </select>
        <select name="priority" class="px-4 py-2 rounded-xl border border-slate-200 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Semua Prioritas</option>
            <option value="high"   {{ request('priority')=='high'   ? 'selected':'' }}>Tinggi</option>
            <option value="medium" {{ request('priority')=='medium' ? 'selected':'' }}>Sedang</option>
            <option value="low"    {{ request('priority')=='low'    ? 'selected':'' }}>Rendah</option>
        </select>
        <select name="category" class="px-4 py-2 rounded-xl border border-slate-200 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Semua Kategori</option>
            <option value="facility"    {{ request('category')=='facility'    ? 'selected':'' }}>Fasilitas</option>
            <option value="security"    {{ request('category')=='security'    ? 'selected':'' }}>Keamanan</option>
            <option value="cleanliness" {{ request('category')=='cleanliness' ? 'selected':'' }}>Kebersihan</option>
            <option value="noise"       {{ request('category')=='noise'       ? 'selected':'' }}>Kebisingan</option>
            <option value="other"       {{ request('category')=='other'       ? 'selected':'' }}>Lainnya</option>
        </select>
        <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-sm font-medium rounded-xl hover:bg-slate-700">Filter</button>
        @if(request()->hasAny(['search','status','priority','category']))
        <a href="{{ route('admin.complaints.index') }}" class="px-4 py-2 bg-slate-100 text-slate-600 text-sm font-medium rounded-xl hover:bg-slate-200">Reset</a>
        @endif
    </form>
</div>

<div class="space-y-3">
    @forelse($complaints as $complaint)
    @php
    $ss = ['new'=>'bg-blue-100 text-blue-700','in_progress'=>'bg-amber-100 text-amber-700','resolved'=>'bg-green-100 text-green-700','rejected'=>'bg-red-100 text-red-700'];
    $ps = ['low'=>'bg-green-100 text-green-700','medium'=>'bg-amber-100 text-amber-700','high'=>'bg-red-100 text-red-700'];
    @endphp
    <div class="bg-white rounded-2xl border border-slate-200 p-5 hover:shadow-md transition-shadow">
        <div class="flex items-start gap-4">
            <img src="{{ $complaint->tenant->photo_url }}" alt="" class="w-10 h-10 rounded-full object-cover flex-shrink-0 mt-0.5">
            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-3 flex-wrap">
                    <div>
                        <h3 class="font-semibold text-slate-800">{{ $complaint->title }}</h3>
                        <p class="text-sm text-slate-500 mt-0.5">
                            {{ $complaint->tenant->name }} • Kamar {{ $complaint->tenant->room->number }}
                            • {{ $complaint->created_at->diffForHumans() }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $ps[$complaint->priority] ?? 'bg-slate-100 text-slate-600' }}">
                            {{ $complaint->priority_label }}
                        </span>
                        <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $ss[$complaint->status] ?? 'bg-slate-100 text-slate-600' }}">
                            {{ $complaint->status_label }}
                        </span>
                    </div>
                </div>
                <p class="text-sm text-slate-600 mt-2 line-clamp-2">{{ $complaint->description }}</p>
                <div class="flex items-center gap-4 mt-3">
                    <span class="text-xs text-slate-400 bg-slate-100 px-2 py-0.5 rounded-lg">{{ $complaint->category_label }}</span>
                    @if($complaint->replies->count())
                    <span class="text-xs text-slate-400 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        {{ $complaint->replies->count() }} balasan
                    </span>
                    @endif
                    @if($complaint->handler)
                    <span class="text-xs text-slate-400">Ditangani: {{ $complaint->handler->name }}</span>
                    @endif
                    <a href="{{ route('admin.complaints.show', $complaint) }}" class="ml-auto text-sm font-medium text-blue-600 hover:text-blue-700">
                        Lihat Detail →
                    </a>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-2xl border border-slate-200 p-16 text-center">
        <svg class="w-12 h-12 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
        </svg>
        <p class="font-semibold text-slate-600">Tidak ada keluhan</p>
        <p class="text-sm text-slate-400 mt-1">Semua penghuni merasa nyaman 🎉</p>
    </div>
    @endforelse
</div>

<div>{{ $complaints->links() }}</div>

</div>
@endsection
