@extends('layouts.app')
@section('title','Penghuni')
@section('page-title','Manajemen Penghuni')
@section('breadcrumb') <span class="mx-1">/</span> Penghuni @endsection

@section('content')
<div class="space-y-5">
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <p class="text-sm text-slate-500">Total <span class="font-semibold text-slate-700">{{ $tenants->total() }}</span> penghuni</p>
    <a href="{{ route('admin.tenants.create') }}"
       class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-all shadow-lg shadow-blue-600/20">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Penghuni
    </a>
</div>

<div class="bg-white rounded-2xl border border-slate-200 p-4">
    <form method="GET" class="flex flex-wrap gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, HP..."
               class="flex-1 min-w-48 px-4 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        <select name="status" class="px-4 py-2 rounded-xl border border-slate-200 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Semua Status</option>
            <option value="active"   {{ request('status')=='active'   ? 'selected':'' }}>Aktif</option>
            <option value="inactive" {{ request('status')=='inactive' ? 'selected':'' }}>Tidak Aktif</option>
        </select>
        <select name="gender" class="px-4 py-2 rounded-xl border border-slate-200 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Semua Gender</option>
            <option value="male"   {{ request('gender')=='male'   ? 'selected':'' }}>Laki-laki</option>
            <option value="female" {{ request('gender')=='female' ? 'selected':'' }}>Perempuan</option>
        </select>
        <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-sm font-medium rounded-xl hover:bg-slate-700">Filter</button>
        @if(request()->hasAny(['search','status','gender']))
        <a href="{{ route('admin.tenants.index') }}" class="px-4 py-2 bg-slate-100 text-slate-600 text-sm font-medium rounded-xl hover:bg-slate-200">Reset</a>
        @endif
    </form>
</div>

<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
    @if($tenants->isEmpty())
    <div class="p-16 text-center">
        <svg class="w-12 h-12 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        <p class="font-semibold text-slate-600">Belum ada penghuni</p>
        <p class="text-sm text-slate-400 mt-1">Tambahkan penghuni baru untuk memulai</p>
    </div>
    @else
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-slate-50 border-b border-slate-200">
                <th class="text-left px-6 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Penghuni</th>
                <th class="text-left px-6 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Kamar</th>
                <th class="text-left px-6 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide hidden sm:table-cell">Kontak</th>
                <th class="text-left px-6 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide hidden md:table-cell">Masuk</th>
                <th class="text-left px-6 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Status</th>
                <th class="px-6 py-3.5"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @foreach($tenants as $tenant)
            <tr class="hover:bg-slate-50 transition-colors">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <img src="{{ $tenant->photo_url }}" alt="" class="w-9 h-9 rounded-full object-cover flex-shrink-0">
                        <div>
                            <p class="font-medium text-slate-800">{{ $tenant->name }}</p>
                            <p class="text-xs text-slate-400">{{ $tenant->gender_label }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <span class="font-mono text-xs bg-slate-100 text-slate-700 px-2 py-1 rounded-lg">{{ $tenant->room->number }}</span>
                </td>
                <td class="px-6 py-4 hidden sm:table-cell">
                    <p class="text-slate-600">{{ $tenant->phone }}</p>
                    @if($tenant->email)<p class="text-xs text-slate-400">{{ $tenant->email }}</p>@endif
                </td>
                <td class="px-6 py-4 text-slate-600 hidden md:table-cell">{{ $tenant->start_date->format('d M Y') }}</td>
                <td class="px-6 py-4">
                    <span class="inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-1 rounded-full {{ $tenant->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $tenant->status === 'active' ? 'bg-green-500' : 'bg-slate-400' }}"></span>
                        {{ $tenant->status_label }}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-1.5 justify-end">
                        <a href="{{ route('admin.tenants.show', $tenant) }}" class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Detail">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </a>
                        <a href="{{ route('admin.tenants.edit', $tenant) }}" class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Edit">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        <form method="POST" action="{{ route('admin.tenants.destroy', $tenant) }}" onsubmit="return confirm('Hapus penghuni {{ $tenant->name }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="px-6 py-4 border-t border-slate-100">{{ $tenants->links() }}</div>
    @endif
</div>
</div>
@endsection
