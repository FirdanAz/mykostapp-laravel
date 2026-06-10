@extends('layouts.app')
@section('title','Kamar '.$room->number)
@section('page-title','Detail Kamar')
@section('breadcrumb') <span class="mx-1">/</span> <a href="{{ route('rooms.index') }}" class="hover:text-slate-600">Kamar</a> <span class="mx-1">/</span> {{ $room->number }} @endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Left --}}
    <div class="lg:col-span-2 space-y-5">
        {{-- Photos --}}
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
            @if($room->photos && count($room->photos))
            <div class="h-64 bg-slate-100">
                <img src="{{ asset('storage/'.$room->photos[0]) }}" alt="" class="w-full h-full object-cover" id="main-photo">
            </div>
            @if(count($room->photos) > 1)
            <div class="flex gap-2 p-3 border-t border-slate-100 overflow-x-auto">
                @foreach($room->photos as $i => $photo)
                <button onclick="document.getElementById('main-photo').src='{{ asset('storage/'.$photo) }}'"
                        class="flex-shrink-0 w-16 h-16 rounded-xl overflow-hidden border-2 border-transparent hover:border-blue-500 transition-colors">
                    <img src="{{ asset('storage/'.$photo) }}" alt="" class="w-full h-full object-cover">
                </button>
                @endforeach
            </div>
            @endif
            @else
            <div class="h-48 flex items-center justify-center bg-slate-50">
                <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            @endif
        </div>

        {{-- Info --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-slate-900">Kamar {{ $room->number }}</h2>
                @php $sc = ['available'=>'bg-green-100 text-green-700','occupied'=>'bg-blue-100 text-blue-700','maintenance'=>'bg-amber-100 text-amber-700']; @endphp
                <span class="text-sm font-medium px-3 py-1 rounded-full {{ $sc[$room->status] }}">{{ $room->status_label }}</span>
            </div>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div class="p-3 bg-slate-50 rounded-xl">
                    <p class="text-xs text-slate-500">Lantai</p>
                    <p class="font-semibold text-slate-800 mt-0.5">Lantai {{ $room->floor }}</p>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl">
                    <p class="text-xs text-slate-500">Harga / Bulan</p>
                    <p class="font-semibold text-blue-600 mt-0.5">{{ $room->formatted_price }}</p>
                </div>
            </div>
            @if($room->description)
            <p class="text-sm text-slate-600">{{ $room->description }}</p>
            @endif
        </div>

        {{-- Facilities --}}
        @if($room->facilities->count())
        <div class="bg-white rounded-2xl border border-slate-200 p-6">
            <h3 class="font-semibold text-slate-800 mb-4">Fasilitas</h3>
            <div class="flex flex-wrap gap-2">
                @foreach($room->facilities as $f)
                <span class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-700 text-sm px-3 py-1.5 rounded-xl font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ $f->name }}
                </span>
                @endforeach
            </div>
        </div>
        @endif

        {{-- History --}}
        @if($room->tenants->count())
        <div class="bg-white rounded-2xl border border-slate-200 p-6">
            <h3 class="font-semibold text-slate-800 mb-4">Riwayat Penghuni</h3>
            <div class="space-y-3">
                @foreach($room->tenants as $tenant)
                <a href="{{ route('tenants.show', $tenant) }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 transition-colors">
                    <img src="{{ $tenant->photo_url }}" alt="" class="w-10 h-10 rounded-full object-cover">
                    <div class="flex-1">
                        <p class="font-medium text-slate-800 text-sm">{{ $tenant->name }}</p>
                        <p class="text-xs text-slate-400">{{ $tenant->start_date->format('d M Y') }} — {{ $tenant->end_date?->format('d M Y') ?? 'Sekarang' }}</p>
                    </div>
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $tenant->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600' }}">
                        {{ $tenant->status_label }}
                    </span>
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- Right Sidebar --}}
    <div class="space-y-4">
        {{-- Actions --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5 space-y-2.5">
            <h3 class="font-semibold text-slate-800 text-sm mb-3">Aksi</h3>
            <a href="{{ route('rooms.edit', $room) }}"
               class="w-full flex items-center justify-center gap-2 bg-blue-600 text-white text-sm font-semibold py-2.5 rounded-xl hover:bg-blue-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit Kamar
            </a>
            @if($room->status === 'available')
            <a href="{{ route('tenants.create', ['room_id' => $room->id]) }}"
               class="w-full flex items-center justify-center gap-2 bg-green-600 text-white text-sm font-semibold py-2.5 rounded-xl hover:bg-green-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                Tambah Penghuni
            </a>
            @endif
            <form method="POST" action="{{ route('rooms.destroy', $room) }}" onsubmit="return confirm('Yakin hapus kamar ini?')">
                @csrf @method('DELETE')
                <button type="submit" class="w-full flex items-center justify-center gap-2 bg-red-50 text-red-600 text-sm font-semibold py-2.5 rounded-xl hover:bg-red-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Hapus Kamar
                </button>
            </form>
        </div>

        {{-- Active Tenant --}}
        @if($room->activeTenant)
        <div class="bg-blue-50 rounded-2xl border border-blue-200 p-5">
            <h3 class="font-semibold text-blue-800 text-sm mb-3">Penghuni Aktif</h3>
            <div class="flex items-center gap-3">
                <img src="{{ $room->activeTenant->photo_url }}" alt="" class="w-12 h-12 rounded-full object-cover ring-2 ring-blue-300">
                <div>
                    <p class="font-semibold text-blue-900">{{ $room->activeTenant->name }}</p>
                    <p class="text-xs text-blue-600">{{ $room->activeTenant->phone }}</p>
                    <p class="text-xs text-blue-500 mt-0.5">Masuk: {{ $room->activeTenant->start_date->format('d M Y') }}</p>
                </div>
            </div>
            <a href="{{ route('tenants.show', $room->activeTenant) }}"
               class="mt-3 w-full flex items-center justify-center text-xs font-medium text-blue-700 hover:text-blue-900">
                Lihat Profil Penghuni →
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
