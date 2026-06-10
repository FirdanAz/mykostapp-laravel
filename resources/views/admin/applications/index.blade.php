@extends('layouts.app')
@section('title', 'Kelola Pengajuan Sewa')
@section('page-title', 'Pengajuan Sewa Masuk')

@section('content')
<div class="space-y-6">

    {{-- Tabs --}}
    <div class="border-b border-slate-200">
        <nav class="-mb-px flex space-x-6" aria-label="Tabs">
            <a href="{{ route('admin.applications.index') }}"
               class="shrink-0 border-b-2 px-1 pb-4 text-sm font-medium {{ !request('status') || request('status') === 'pending' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700' }}">
                Menunggu Persetujuan
                @php
                    $pendingCount = \App\Models\RentalApplication::whereHas('room', fn($q)=>$q->where('kost_id',$kost->id))->where('status','pending')->count();
                @endphp
                @if($pendingCount > 0)
                <span class="ml-2 rounded-full bg-blue-100 px-2 py-0.5 text-xs font-bold text-blue-600">{{ $pendingCount }}</span>
                @endif
            </a>
            <a href="{{ route('admin.applications.index', ['status' => 'approved']) }}"
               class="shrink-0 border-b-2 px-1 pb-4 text-sm font-medium {{ request('status') === 'approved' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700' }}">
                Disetujui
            </a>
            <a href="{{ route('admin.applications.index', ['status' => 'rejected']) }}"
               class="shrink-0 border-b-2 px-1 pb-4 text-sm font-medium {{ request('status') === 'rejected' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700' }}">
                Ditolak
            </a>
        </nav>
    </div>

    @if($applications->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
            </svg>
        </div>
        <h3 class="text-lg font-semibold text-slate-800 mb-1">Tidak ada data pengajuan</h3>
        <p class="text-slate-500 text-sm max-w-sm mx-auto">
            Tidak ada pengajuan sewa untuk kategori status ini.
        </p>
    </div>
    @else
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Pencari Kos (User)</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Kamar Pilihan</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Tanggal Masuk</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Durasi</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($applications as $app)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img src="{{ $app->user->avatar_url }}" class="w-8 h-8 rounded-full object-cover">
                                <div>
                                    <div class="text-sm font-semibold text-slate-800">{{ $app->name }}</div>
                                    <div class="text-xs text-slate-500">{{ $app->phone }} • {{ $app->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-700">
                            <strong>Kamar {{ $app->room->number }}</strong> (Lantai {{ $app->room->floor }})
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-700">
                            {{ $app->start_date?->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-700">
                            {{ $app->duration_months }} Bulan
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusClasses = [
                                    'pending' => 'bg-amber-50 text-amber-700 ring-amber-600/10',
                                    'approved' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/10',
                                    'rejected' => 'bg-red-50 text-red-700 ring-red-600/10',
                                ];
                            @endphp
                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset {{ $statusClasses[$app->status] ?? 'bg-slate-50 text-slate-700' }}">
                                {{ $app->status_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.applications.show', $app) }}"
                               class="inline-flex items-center px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 text-xs font-semibold rounded-lg transition-colors">
                                Detail / Tinjau
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($applications->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $applications->links() }}
        </div>
        @endif
    </div>
    @endif
</div>
@endsection
