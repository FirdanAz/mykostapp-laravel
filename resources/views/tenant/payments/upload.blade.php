@extends('layouts.tenant')
@section('title', 'Upload Bukti Pembayaran')
@section('page-title', 'Upload Bukti Bayar')
@section('breadcrumb')
    <span class="mx-1">/</span> <a href="{{ route('tenant.invoices.index') }}" class="hover:text-slate-600">Tagihan</a>
    <span class="mx-1">/</span> Bayar
@endsection

@section('content')
<div class="max-w-lg space-y-5">

{{-- Invoice Summary --}}
<div class="bg-white rounded-2xl border border-slate-200 p-5">
    <p class="text-xs text-slate-400 mb-1">Membayar untuk</p>
    <p class="font-semibold text-slate-800">{{ $invoice->invoice_number }}</p>
    <p class="text-xs text-slate-500 mt-1">Periode: {{ $invoice->period_start?->format('d M') }} — {{ $invoice->period_end?->format('d M Y') }}</p>
    <p class="text-2xl font-bold text-slate-900 mt-3">{{ $invoice->formatted_amount }}</p>
    @if($invoice->status === 'overdue')
    <p class="text-xs text-red-600 mt-1 font-medium">⚠️ Tagihan ini sudah melewati jatuh tempo</p>
    @endif
</div>

{{-- Upload Form --}}
<form method="POST" action="{{ route('tenant.payments.store', $invoice) }}" enctype="multipart/form-data" class="space-y-5">
    @csrf

    <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4">
        <h3 class="font-semibold text-slate-800">Upload Bukti Transfer</h3>

        {{-- Jumlah --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">
                Jumlah yang Dibayar <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-medium text-slate-500">Rp</span>
                <input type="number" name="amount" value="{{ old('amount', $invoice->amount) }}" required min="1"
                       class="w-full pl-12 pr-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('amount') border-red-400 @enderror">
            </div>
            @error('amount') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Bukti Pembayaran --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">
                Bukti Pembayaran <span class="text-red-500">*</span>
            </label>
            <div id="drop-zone"
                 class="border-2 border-dashed border-slate-300 rounded-xl p-8 text-center cursor-pointer hover:border-emerald-400 hover:bg-emerald-50 transition-all"
                 onclick="document.getElementById('proof-file').click()">
                <svg class="w-10 h-10 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="text-sm font-medium text-slate-600">Klik untuk pilih file</p>
                <p class="text-xs text-slate-400 mt-1">JPG, PNG, atau PDF — Maks. 5 MB</p>
            </div>
            <input type="file" id="proof-file" name="proof_file" accept="image/*,.pdf" class="hidden" required>
            <div id="file-preview" class="mt-3 hidden">
                <div class="flex items-center gap-3 p-3 bg-emerald-50 border border-emerald-200 rounded-xl">
                    <svg class="w-8 h-8 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="flex-1 min-w-0">
                        <p id="file-name" class="text-sm font-medium text-emerald-800 truncate"></p>
                        <p id="file-size" class="text-xs text-emerald-600"></p>
                    </div>
                    <button type="button" onclick="clearFile()" class="text-slate-400 hover:text-red-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
            @error('proof_file') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Catatan --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Catatan <span class="text-slate-400 font-normal">(opsional)</span></label>
            <textarea name="notes" rows="2"
                      class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 resize-none"
                      placeholder="cth: Transfer via BCA, nomor referensi 1234...">{{ old('notes') }}</textarea>
        </div>
    </div>

    <div class="flex gap-3">
        <button type="submit"
                class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 px-4 rounded-xl transition-all shadow-lg shadow-emerald-600/20 flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
            </svg>
            Upload Bukti Pembayaran
        </button>
        <a href="{{ route('tenant.invoices.show', $invoice) }}"
           class="px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium rounded-xl transition-colors text-sm">
            Batal
        </a>
    </div>
</form>
</div>
@endsection

@push('scripts')
<script>
const input = document.getElementById('proof-file');
const preview = document.getElementById('file-preview');
const nameEl = document.getElementById('file-name');
const sizeEl = document.getElementById('file-size');
const dropZone = document.getElementById('drop-zone');

input.addEventListener('change', function() {
    if (this.files[0]) {
        const file = this.files[0];
        nameEl.textContent = file.name;
        sizeEl.textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';
        preview.classList.remove('hidden');
        dropZone.classList.add('hidden');
    }
});

function clearFile() {
    input.value = '';
    preview.classList.add('hidden');
    dropZone.classList.remove('hidden');
}
</script>
@endpush
