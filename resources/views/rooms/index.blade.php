@extends('layouts.app')
@section('title','Manajemen Kamar')
@section('page-title','Manajemen Kamar')
@section('breadcrumb') <span class="mx-1">/</span> Kamar @endsection

@section('content')
<div class="space-y-5">

{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <p class="text-sm text-slate-500">Total <span class="font-semibold text-slate-700">{{ $rooms->total() }}</span> kamar ditemukan</p>
    </div>
    <a href="{{ route('admin.rooms.create') }}"
       class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-all shadow-lg shadow-blue-600/20">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Kamar
    </a>
</div>

{{-- Filters --}}
<div class="bg-white rounded-2xl border border-slate-200 p-4">
    <form method="GET" class="flex flex-wrap gap-3">
        <div class="flex-1 min-w-48">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari nomor kamar..."
                   class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <select name="status" class="px-4 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
            <option value="">Semua Status</option>
            <option value="available"   {{ request('status')=='available'   ? 'selected':'' }}>Tersedia</option>
            <option value="occupied"    {{ request('status')=='occupied'    ? 'selected':'' }}>Terisi</option>
            <option value="maintenance" {{ request('status')=='maintenance' ? 'selected':'' }}>Maintenance</option>
        </select>
        <select name="floor" class="px-4 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
            <option value="">Semua Lantai</option>
            @foreach($floors as $floor)
            <option value="{{ $floor }}" {{ request('floor')==$floor ? 'selected':'' }}>Lantai {{ $floor }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-sm font-medium rounded-xl hover:bg-slate-700 transition-colors">Filter</button>
        @if(request()->hasAny(['search','status','floor']))
        <a href="{{ route('admin.rooms.index') }}" class="px-4 py-2 bg-slate-100 text-slate-600 text-sm font-medium rounded-xl hover:bg-slate-200 transition-colors">Reset</a>
        @endif
    </form>
</div>

{{-- Grid --}}
@if($rooms->isEmpty())
<div class="bg-white rounded-2xl border border-slate-200 p-16 text-center">
    <svg class="w-12 h-12 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
    </svg>
    <p class="font-semibold text-slate-600">Tidak ada kamar ditemukan</p>
    <p class="text-sm text-slate-400 mt-1">Tambahkan kamar baru untuk memulai</p>
    <a href="{{ route('admin.rooms.create') }}" class="mt-4 inline-flex items-center gap-2 bg-blue-600 text-white text-sm font-medium px-4 py-2 rounded-xl hover:bg-blue-700 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Kamar
    </a>
</div>
@else
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
    @foreach($rooms as $room)
    @php
    $statusStyle = [
        'available'   => ['border'=>'border-green-200',  'badge'=>'bg-green-100 text-green-700',   'dot'=>'bg-green-500'],
        'occupied'    => ['border'=>'border-blue-200',   'badge'=>'bg-blue-100 text-blue-700',     'dot'=>'bg-blue-500'],
        'maintenance' => ['border'=>'border-amber-200',  'badge'=>'bg-amber-100 text-amber-700',   'dot'=>'bg-amber-500'],
    ];
    $s = $statusStyle[$room->status];
    @endphp
    <div class="bg-white rounded-2xl border {{ $s['border'] }} overflow-hidden hover:shadow-lg transition-all duration-200 group">
        {{-- Photo --}}
        <div class="h-36 bg-slate-100 relative overflow-hidden">
            @if($room->first_photo)
            <img src="{{ $room->first_photo }}" alt="" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
            @else
            <div class="w-full h-full flex items-center justify-center">
                <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </div>
            @endif
            <div class="absolute top-2 right-2">
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full {{ $s['badge'] }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $s['dot'] }}"></span>
                    {{ $room->status_label }}
                </span>
            </div>
            <div class="absolute top-2 left-2 bg-slate-900/70 text-white text-xs font-bold px-2 py-1 rounded-lg">
                Lantai {{ $room->floor }}
            </div>
        </div>

        <div class="p-4">
            <div class="flex items-start justify-between mb-2">
                <div>
                    <h3 class="font-bold text-slate-800">Kamar {{ $room->number }}</h3>
                    <p class="text-blue-600 font-semibold text-sm mt-0.5">{{ $room->formatted_price }}<span class="text-slate-400 font-normal">/bln</span></p>
                </div>
            </div>

            @if($room->facilities->count())
            <div class="flex flex-wrap gap-1 mb-3">
                @foreach($room->facilities->take(3) as $f)
                <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full">{{ $f->name }}</span>
                @endforeach
                @if($room->facilities->count() > 3)
                <span class="text-xs bg-slate-100 text-slate-500 px-2 py-0.5 rounded-full">+{{ $room->facilities->count()-3 }}</span>
                @endif
            </div>
            @endif

            @if($room->status === 'occupied' && $room->activeTenant)
            <div class="flex items-center gap-2 mb-3 p-2 bg-blue-50 rounded-lg">
                <img src="{{ $room->activeTenant->photo_url }}" alt="" class="w-6 h-6 rounded-full object-cover">
                <p class="text-xs text-blue-700 font-medium truncate">{{ $room->activeTenant->name }}</p>
            </div>
            @endif

            <div class="flex gap-2 pt-2 border-t border-slate-100">
                <a href="{{ route('admin.rooms.show', $room) }}"
                   class="flex-1 text-center text-xs font-medium py-1.5 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 transition-colors">Detail</a>
                <a href="{{ route('admin.rooms.edit', $room) }}"
                   class="flex-1 text-center text-xs font-medium py-1.5 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 transition-colors">Edit</a>
                <form method="POST" action="{{ route('admin.rooms.destroy', $room) }}" onsubmit="return confirm('Hapus kamar {{ $room->number }}?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors text-xs font-medium">Hapus</button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Pagination --}}
<div class="flex justify-center">
    {{ $rooms->links() }}
</div>
@endif

</div>
@endsection
