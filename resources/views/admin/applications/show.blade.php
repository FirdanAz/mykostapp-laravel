@extends('layouts.app')
@section('title', 'Tinjau Pengajuan Sewa')
@section('page-title', 'Detail Pengajuan Sewa')

@section('breadcrumb')
<span class="mx-1">/</span>
<a href="{{ route('admin.applications.index') }}" class="hover:text-slate-600">Pengajuan Sewa</a>
<span class="mx-1">/</span>
Tinjau
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Kolom Kiri: Informasi Detail Pengaju --}}
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
            <div class="border-b border-slate-100 pb-4 mb-5 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-slate-800">Dokumen & Identitas Pengaju</h3>
                    <p class="text-xs text-slate-500 mt-1">Diajukan pada {{ $application->created_at->format('d M Y H:i') }}</p>
                </div>
                <div>
                    @php
                        $statusClasses = [
                            'pending' => 'bg-amber-50 text-amber-700 ring-amber-600/10',
                            'approved' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/10',
                            'rejected' => 'bg-red-50 text-red-700 ring-red-600/10',
                        ];
                    @endphp
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset {{ $statusClasses[$application->status] ?? 'bg-slate-50 text-slate-700' }}">
                        {{ $application->status_label }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Data Diri --}}
                <div class="space-y-4">
                    <div>
                        <span class="text-xs text-slate-400 block font-medium">Nama Lengkap</span>
                        <span class="text-sm font-semibold text-slate-800">{{ $application->name }}</span>
                    </div>

                    <div>
                        <span class="text-xs text-slate-400 block font-medium">Nomor KTP (NIK)</span>
                        <span class="text-sm font-semibold text-slate-800">{{ $application->id_card }}</span>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-xs text-slate-400 block font-medium">Jenis Kelamin</span>
                            <span class="text-sm font-semibold text-slate-800">{{ $application->gender_label }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400 block font-medium">Nomor HP</span>
                            <span class="text-sm font-semibold text-slate-800">{{ $application->phone }}</span>
                        </div>
                    </div>

                    <div>
                        <span class="text-xs text-slate-400 block font-medium">Alamat Email</span>
                        <span class="text-sm font-semibold text-slate-800">{{ $application->email }}</span>
                    </div>

                    <div>
                        <span class="text-xs text-slate-400 block font-medium">Alamat Asal</span>
                        <p class="text-sm font-medium text-slate-700 leading-relaxed mt-0.5">{{ $application->address }}</p>
                    </div>
                </div>

                {{-- Preview KTP --}}
                <div class="space-y-3">
                    <span class="text-xs text-slate-400 block font-medium">Lampiran Foto KTP</span>
                    <div class="relative group border border-slate-200 rounded-xl overflow-hidden cursor-pointer" onclick="openKtpModal()">
                        <img src="{{ $application->id_card_photo_url }}" alt="KTP Scan" class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                        <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity duration-200 text-white font-medium text-xs gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Klik untuk Zoom
                        </div>
                    </div>
                </div>
            </div>

            @if($application->notes)
            <div class="mt-6 pt-5 border-t border-slate-100">
                <span class="text-xs text-slate-400 block font-medium">Catatan Tambahan dari Pengaju</span>
                <p class="text-sm text-slate-700 mt-1 bg-slate-50 rounded-xl p-3 border border-slate-100">{{ $application->notes }}</p>
            </div>
            @endif

            @if($application->status === 'rejected')
            <div class="mt-6 pt-5 border-t border-slate-100 bg-red-50/50 rounded-xl p-4 border border-red-100/50">
                <span class="text-xs text-red-500 block font-bold">Alasan Penolakan</span>
                <p class="text-sm text-slate-700 mt-1 font-medium">{{ $application->rejection_reason }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Kolom Kanan: Rencana Sewa & Aksi Persetujuan --}}
    <div class="space-y-6">
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-4">
            <h4 class="text-sm font-bold text-slate-800">Detail Sewa Kamar</h4>

            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <span class="text-xs text-slate-500 font-medium">Kamar yang Dipilih</span>
                <span class="text-sm font-bold text-slate-800">Kamar {{ $application->room->number }}</span>
            </div>

            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <span class="text-xs text-slate-500 font-medium">Harga Bulanan</span>
                <span class="text-sm font-bold text-blue-600">{{ $application->room->formatted_price }}</span>
            </div>

            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <span class="text-xs text-slate-500 font-medium">Tanggal Mulai Masuk</span>
                <span class="text-sm font-bold text-slate-800">{{ $application->start_date?->format('d M Y') }}</span>
            </div>

            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <span class="text-xs text-slate-500 font-medium">Durasi Sewa</span>
                <span class="text-sm font-bold text-slate-800">{{ $application->duration_months }} Bulan</span>
            </div>

            @if($application->status === 'pending')
            <div class="pt-3 space-y-3">
                {{-- Form Approve --}}
                <form method="POST" action="{{ route('admin.applications.approve', $application) }}" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui pengajuan sewa ini? Kamar akan di-setting terisi dan invoice bulan pertama otomatis terbit.');">
                    @csrf
                    <button type="submit"
                            class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-4 rounded-xl transition-all text-sm shadow-lg shadow-emerald-600/10 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Setujui Pengajuan
                    </button>
                </form>

                {{-- Tombol Tolak (Toggles form) --}}
                <button type="button" onclick="toggleRejectionForm()"
                        class="w-full bg-white hover:bg-red-50 text-red-600 border border-red-200 font-semibold py-2.5 px-4 rounded-xl transition-all text-sm flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Tolak Pengajuan
                </button>

                {{-- Form Rejection (Hidden by default) --}}
                <div id="rejection-form" class="hidden mt-4 p-4 border border-red-100 rounded-xl bg-red-50/30 space-y-3">
                    <form method="POST" action="{{ route('admin.applications.reject', $application) }}">
                        @csrf
                        <label class="block text-xs font-semibold text-red-700 mb-1">Alasan Penolakan <span class="text-red-500">*</span></label>
                        <textarea name="rejection_reason" rows="3" required
                                  class="w-full px-3 py-2 rounded-lg border border-red-200 text-xs focus:outline-none focus:ring-1 focus:ring-red-400 focus:border-transparent"
                                  placeholder="Tulis alasan penolakan secara sopan..."></textarea>
                        <button type="submit"
                                class="w-full mt-2 bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-3 rounded-lg text-xs transition-colors">
                            Kirim & Tolak Pengajuan
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>

</div>

{{-- MODAL ZOOM KTP --}}
<div id="ktp-modal" class="hidden fixed inset-0 z-[100] bg-slate-900/80 backdrop-blur-sm flex items-center justify-center p-4" onclick="closeKtpModal()">
    <div class="relative max-w-3xl w-full" onclick="event.stopPropagation()">
        <button class="absolute -top-10 right-0 text-white hover:text-slate-200 flex items-center gap-1 text-sm font-semibold" onclick="closeKtpModal()">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            Tutup
        </button>
        <img src="{{ $application->id_card_photo_url }}" alt="KTP Scan Full" class="w-full max-h-[80vh] object-contain rounded-xl border border-white/20 shadow-2xl">
    </div>
</div>

<script>
function openKtpModal() {
    document.getElementById('ktp-modal').classList.remove('hidden');
}

function closeKtpModal() {
    document.getElementById('ktp-modal').classList.add('hidden');
}

function toggleRejectionForm() {
    const form = document.getElementById('rejection-form');
    if (form.classList.contains('hidden')) {
        form.classList.remove('hidden');
    } else {
        form.classList.add('hidden');
    }
}
</script>
@endsection
