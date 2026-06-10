@extends('layouts.app')
@section('title','Upload Bukti Pembayaran')
@section('page-title','Upload Bukti Pembayaran')
@section('breadcrumb') <span class="mx-1">/</span> <a href="{{ route('admin.invoices.index') }}" class="hover:text-slate-600">Tagihan</a> <span class="mx-1">/</span> Upload Bukti @endsection

@section('content')
<div class="max-w-2xl">

    {{-- Invoice Summary --}}
    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5 mb-5">
        <div class="flex items-start gap-4">
            <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <p class="font-semibold text-blue-900">{{ $invoice->invoice_number }}</p>
                <p class="text-sm text-blue-700 mt-0.5">{{ $invoice->tenant->name }} — Kamar {{ $invoice->tenant->room->number }}</p>
                <p class="text-lg font-bold text-blue-800 mt-1">{{ $invoice->formatted_amount }}</p>
                <p class="text-xs text-blue-600 mt-0.5">Jatuh tempo: {{ $invoice->due_date->format('d M Y') }}</p>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.payments.store', $invoice) }}" enctype="multipart/form-data" class="space-y-5">
        @csrf

        <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-5">
            <h3 class="font-semibold text-slate-800 text-sm pb-3 border-b border-slate-100">Detail Pembayaran</h3>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Nominal yang Dibayar (Rp) <span class="text-red-500">*</span></label>
                <input type="number" name="amount" value="{{ old('amount', $invoice->amount) }}" required min="1" step="1000"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('amount') border-red-400 @enderror">
                @error('amount') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Bukti Pembayaran <span class="text-red-500">*</span></label>
                <div id="proof-drop-area"
                     class="border-2 border-dashed border-slate-300 rounded-xl p-8 text-center cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition-all"
                     onclick="document.getElementById('proof-file').click()">
                    <div id="drop-placeholder">
                        <svg class="w-10 h-10 text-slate-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        <p class="font-medium text-slate-600">Klik atau drag foto bukti pembayaran</p>
                        <p class="text-xs text-slate-400 mt-1">JPG, PNG, WEBP — Maks. 5MB</p>
                        <p class="text-xs text-slate-400 mt-0.5">Struk ATM, screenshot m-banking, dll.</p>
                    </div>
                    <img id="proof-preview" src="" alt="" class="hidden max-h-48 mx-auto rounded-xl">
                </div>
                <input type="file" id="proof-file" name="proof_file" accept="image/*,.pdf" class="hidden"
                       onchange="previewProof(this)" required>
                @error('proof_file') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Catatan</label>
                <textarea name="notes" rows="2"
                          class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                          placeholder="Opsional: nomor referensi transfer, dll.">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="flex-1 sm:flex-none px-8 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl transition-all shadow-lg shadow-blue-600/20">
                Kirim Bukti Pembayaran
            </button>
            <a href="{{ route('admin.invoices.show', $invoice) }}" class="flex-1 sm:flex-none px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl transition-colors text-center">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function previewProof(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = e => {
                document.getElementById('drop-placeholder').classList.add('hidden');
                const img = document.getElementById('proof-preview');
                img.src = e.target.result;
                img.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            document.getElementById('drop-placeholder').querySelector('p').textContent = '📄 ' + file.name;
        }
    }
}
</script>
@endpush
