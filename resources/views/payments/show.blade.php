@extends('layouts.app')
@section('title','Verifikasi Pembayaran')
@section('page-title','Detail Pembayaran')
@section('breadcrumb') <span class="mx-1">/</span> <a href="{{ route('admin.payments.index') }}" class="hover:text-slate-600">Pembayaran</a> <span class="mx-1">/</span> Detail @endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Proof Image --}}
    <div class="lg:col-span-2 space-y-5">
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <h3 class="font-semibold text-slate-800">Bukti Pembayaran</h3>
                <a href="{{ $payment->proof_url }}" target="_blank"
                   class="inline-flex items-center gap-1.5 text-xs text-blue-600 hover:text-blue-700 font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    Buka di Tab Baru
                </a>
            </div>
            <div class="p-6 flex items-center justify-center bg-slate-50 min-h-64">
                <img src="{{ $payment->proof_url }}" alt="Bukti Pembayaran"
                     class="max-w-full max-h-96 rounded-xl shadow-lg object-contain"
                     onerror="this.style.display='none'; document.getElementById('no-preview').style.display='flex'">
                <div id="no-preview" class="hidden flex-col items-center gap-3 text-slate-400">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <a href="{{ $payment->proof_url }}" target="_blank" class="text-blue-600 hover:text-blue-700 text-sm font-medium">Unduh File</a>
                </div>
            </div>
        </div>

        {{-- Invoice Detail --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-6">
            <h3 class="font-semibold text-slate-800 mb-4 pb-3 border-b border-slate-100">Informasi Invoice</h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-xs text-slate-400 mb-0.5">Nomor Invoice</p>
                    <p class="font-mono font-semibold text-slate-800">{{ $payment->invoice->invoice_number }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 mb-0.5">Penghuni</p>
                    <p class="font-semibold text-slate-800">{{ $payment->invoice->tenant->name }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 mb-0.5">Kamar</p>
                    <p class="font-semibold text-slate-800">Kamar {{ $payment->invoice->tenant->room->number }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 mb-0.5">Periode</p>
                    <p class="font-semibold text-slate-800">{{ $payment->invoice->period_start->translatedFormat('F Y') }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 mb-0.5">Tagihan</p>
                    <p class="font-semibold text-slate-800">{{ $payment->invoice->formatted_amount }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 mb-0.5">Jumlah Dibayar</p>
                    <p class="font-bold text-blue-600 text-base">{{ $payment->formatted_amount }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 mb-0.5">Waktu Upload</p>
                    <p class="font-semibold text-slate-800">{{ $payment->created_at->format('d M Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 mb-0.5">Status</p>
                    @php $ps = ['pending'=>'bg-amber-100 text-amber-700','verified'=>'bg-green-100 text-green-700','rejected'=>'bg-red-100 text-red-700']; @endphp
                    <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $ps[$payment->status] ?? 'bg-slate-100 text-slate-600' }}">
                        {{ $payment->status_label }}
                    </span>
                </div>
            </div>
            @if($payment->rejection_reason)
            <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-xl">
                <p class="text-xs font-semibold text-red-600 mb-1">Alasan Penolakan</p>
                <p class="text-sm text-red-700">{{ $payment->rejection_reason }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Actions Sidebar --}}
    <div class="space-y-4">
        @if($payment->status === 'pending')
        {{-- Verify --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <h3 class="font-semibold text-slate-800 text-sm mb-4">Verifikasi Pembayaran</h3>
            <div class="p-4 bg-green-50 border border-green-200 rounded-xl mb-4">
                <p class="text-sm text-green-700">Pastikan nominal, tanggal, dan identitas pengirim sesuai sebelum menyetujui.</p>
            </div>
            <form method="POST" action="{{ route('admin.payments.verify', $payment) }}">
                @csrf
                <button type="submit" onclick="return confirm('Setujui pembayaran ini?')"
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded-xl text-sm transition-colors shadow-lg shadow-green-600/20">
                    ✓ Setujui Pembayaran
                </button>
            </form>
        </div>

        {{-- Reject --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <h3 class="font-semibold text-slate-800 text-sm mb-4">Tolak Pembayaran</h3>
            <form method="POST" action="{{ route('admin.payments.reject', $payment) }}" class="space-y-3"
                  onsubmit="return confirm('Tolak pembayaran ini? Penghuni perlu upload ulang bukti.')">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Alasan Penolakan <span class="text-red-500">*</span></label>
                    <textarea name="rejection_reason" rows="3" required
                              class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 resize-none @error('rejection_reason') border-red-400 @enderror"
                              placeholder="Contoh: Foto bukti tidak jelas, nominal tidak sesuai, dll.">{{ old('rejection_reason') }}</textarea>
                    @error('rejection_reason') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <button type="submit"
                        class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2.5 rounded-xl text-sm transition-colors">
                    ✕ Tolak Pembayaran
                </button>
            </form>
        </div>

        @elseif($payment->status === 'verified')
        <div class="bg-green-50 border border-green-200 rounded-2xl p-5 text-center">
            <div class="w-12 h-12 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <p class="font-bold text-green-800">Pembayaran Disetujui</p>
            <p class="text-xs text-green-600 mt-1">{{ $payment->verified_at?->format('d M Y H:i') }}</p>
            <p class="text-xs text-green-600">oleh {{ $payment->verifiedBy?->name }}</p>
        </div>

        @elseif($payment->status === 'rejected')
        <div class="bg-red-50 border border-red-200 rounded-2xl p-5 text-center">
            <div class="w-12 h-12 bg-red-600 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <p class="font-bold text-red-800">Pembayaran Ditolak</p>
            <p class="text-xs text-red-600 mt-2">{{ $payment->rejection_reason }}</p>
        </div>
        @endif

        <a href="{{ route('admin.invoices.show', $payment->invoice) }}"
           class="w-full flex items-center justify-center gap-2 bg-white border border-slate-200 text-slate-700 text-sm font-semibold py-2.5 rounded-xl hover:bg-slate-50 transition-colors">
            ← Kembali ke Invoice
        </a>
    </div>
</div>
@endsection
