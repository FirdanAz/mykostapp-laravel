@extends('layouts.app')
@section('title','Buat Tagihan')
@section('page-title','Buat Tagihan')
@section('breadcrumb') <span class="mx-1">/</span> <a href="{{ route('admin.invoices.index') }}" class="hover:text-slate-600">Tagihan</a> <span class="mx-1">/</span> Buat Baru @endsection

@section('content')
<div class="max-w-2xl">
<form method="POST" action="{{ route('admin.invoices.store') }}" class="space-y-5">
    @csrf

    <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-5">
        <h3 class="font-semibold text-slate-800 text-sm pb-3 border-b border-slate-100">Informasi Tagihan</h3>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Penghuni <span class="text-red-500">*</span></label>
            <select name="tenant_id" required id="tenant-select"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 @error('tenant_id') border-red-400 @enderror">
                <option value="">-- Pilih Penghuni --</option>
                @foreach($tenants as $tenant)
                <option value="{{ $tenant->id }}"
                        data-price="{{ $tenant->room->price }}"
                        {{ old('tenant_id', request('tenant_id')) == $tenant->id ? 'selected' : '' }}>
                    {{ $tenant->name }} — Kamar {{ $tenant->room->number }} ({{ $tenant->room->formatted_price }}/bln)
                </option>
                @endforeach
            </select>
            @error('tenant_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Periode Mulai <span class="text-red-500">*</span></label>
                <input type="date" name="period_start" id="period-start"
                       value="{{ old('period_start', date('Y-m-01')) }}" required
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('period_start') border-red-400 @enderror">
                @error('period_start') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Periode Akhir <span class="text-red-500">*</span></label>
                <input type="date" name="period_end" id="period-end"
                       value="{{ old('period_end', date('Y-m-t')) }}" required
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('period_end') border-red-400 @enderror">
                @error('period_end') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Nominal (Rp) <span class="text-red-500">*</span></label>
                <input type="number" name="amount" id="amount-input"
                       value="{{ old('amount') }}" required min="1" step="1000"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('amount') border-red-400 @enderror"
                       placeholder="800000">
                @error('amount') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Jatuh Tempo <span class="text-red-500">*</span></label>
                <input type="date" name="due_date"
                       value="{{ old('due_date', date('Y-m-10')) }}" required
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('due_date') border-red-400 @enderror">
                @error('due_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Catatan</label>
            <textarea name="notes" rows="3"
                      class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                      placeholder="Catatan tambahan...">{{ old('notes') }}</textarea>
        </div>
    </div>

    {{-- Preview --}}
    <div id="invoice-preview" class="bg-blue-50 border border-blue-200 rounded-2xl p-5 hidden">
        <h4 class="text-sm font-semibold text-blue-800 mb-3">Preview Tagihan</h4>
        <div class="grid grid-cols-2 gap-3 text-sm">
            <div><p class="text-blue-600 text-xs">Penghuni</p><p class="font-semibold text-blue-900" id="prev-tenant">—</p></div>
            <div><p class="text-blue-600 text-xs">Nominal</p><p class="font-semibold text-blue-900" id="prev-amount">—</p></div>
        </div>
    </div>

    <div class="flex gap-3">
        <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl transition-all shadow-lg shadow-blue-600/20">
            Buat Tagihan
        </button>
        <a href="{{ route('admin.invoices.index') }}" class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl transition-colors">
            Batal
        </a>
    </div>
</form>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('tenant-select').addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    const price = opt.dataset.price;
    if (price) {
        document.getElementById('amount-input').value = price;
        document.getElementById('invoice-preview').classList.remove('hidden');
        document.getElementById('prev-tenant').textContent = opt.text;
        document.getElementById('prev-amount').textContent = 'Rp ' + parseInt(price).toLocaleString('id-ID');
    }
});

// Auto-set due date when period start changes
document.getElementById('period-start').addEventListener('change', function() {
    const d = new Date(this.value);
    d.setDate(10);
    document.querySelector('[name=due_date]').value = d.toISOString().split('T')[0];
    // auto-set period end to last day of month
    const end = new Date(d.getFullYear(), d.getMonth() + 1, 0);
    document.getElementById('period-end').value = end.toISOString().split('T')[0];
});
</script>
@endpush
