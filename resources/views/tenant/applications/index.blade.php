@extends('layouts.tenant')
@section('title', 'Daftar Pengajuan Sewa')
@section('page-title', 'Pengajuan Sewa Saya')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Daftar Pengajuan</h2>
            <p class="text-sm text-slate-500 mt-1">Status dan riwayat pengajuan sewa kamar kos Anda</p>
        </div>
        <a href="{{ route('public.kosts.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Ajukan Sewa Baru
        </a>
    </div>

    @if($applications->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 p-8 text-center">
        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <h3 class="text-lg font-semibold text-slate-800 mb-1">Belum ada pengajuan</h3>
        <p class="text-slate-500 text-sm max-w-sm mx-auto mb-6">
            Anda belum pernah mengajukan sewa kamar kos melalui aplikasi. Silakan cari kos untuk memulai pengajuan.
        </p>
        <a href="{{ route('public.kosts.index') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-xl text-sm transition-colors">
            Cari Kamar Kos
        </a>
    </div>
    @else
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Tanggal Pengajuan</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Kos / Kamar</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Tanggal Mulai</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Durasi</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($applications as $app)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ $app->created_at->format('d M Y H:i') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-semibold text-slate-800">{{ $app->room->kost->name ?? 'Kos' }}</div>
                            <div class="text-xs text-slate-500">Kamar {{ $app->room->number }} • Lantai {{ $app->room->floor }}</div>
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
                        <td class="px-6 py-4 text-sm max-w-xs truncate">
                            @if($app->status === 'rejected')
                                <span class="text-red-500 text-xs font-medium block">Ditolak:</span>
                                <span class="text-slate-600 text-xs mt-0.5 block break-words whitespace-normal">{{ $app->rejection_reason }}</span>
                            @elseif($app->status === 'approved')
                                <span class="text-emerald-600 text-xs font-medium">Pengajuan Disetujui! Silakan cek menu Dashboard atau Tagihan Saya untuk mulai membayar.</span>
                            @else
                                <span class="text-slate-400 text-xs">Sedang ditinjau oleh pemilik kos.</span>
                            @endif
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
