@extends('layouts.app')
@section('title','Catat Keluhan')
@section('page-title','Catat Keluhan')
@section('breadcrumb') <span class="mx-1">/</span> <a href="{{ route('complaints.index') }}" class="hover:text-slate-600">Keluhan</a> <span class="mx-1">/</span> Catat Baru @endsection

@section('content')
<div class="max-w-2xl">
<form method="POST" action="{{ route('complaints.store') }}" enctype="multipart/form-data" class="space-y-5">
    @csrf

    <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-5">
        <h3 class="font-semibold text-slate-800 text-sm pb-3 border-b border-slate-100">Detail Keluhan</h3>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Penghuni <span class="text-red-500">*</span></label>
            <select name="tenant_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 @error('tenant_id') border-red-400 @enderror">
                <option value="">-- Pilih Penghuni --</option>
                @foreach($tenants as $tenant)
                <option value="{{ $tenant->id }}" {{ old('tenant_id', request('tenant_id')) == $tenant->id ? 'selected':'' }}>
                    {{ $tenant->name }} — Kamar {{ $tenant->room->number }}
                </option>
                @endforeach
            </select>
            @error('tenant_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Judul Keluhan <span class="text-red-500">*</span></label>
            <input type="text" name="title" value="{{ old('title') }}" required
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('title') border-red-400 @enderror"
                   placeholder="Ringkasan singkat keluhan">
            @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Kategori <span class="text-red-500">*</span></label>
                <select name="category" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="facility"    {{ old('category')=='facility'    ? 'selected':'' }}>Fasilitas</option>
                    <option value="security"    {{ old('category')=='security'    ? 'selected':'' }}>Keamanan</option>
                    <option value="cleanliness" {{ old('category')=='cleanliness' ? 'selected':'' }}>Kebersihan</option>
                    <option value="noise"       {{ old('category')=='noise'       ? 'selected':'' }}>Kebisingan</option>
                    <option value="other"       {{ old('category')=='other'       ? 'selected':'' }}>Lainnya</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Prioritas <span class="text-red-500">*</span></label>
                <select name="priority" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="low"    {{ old('priority')=='low'    ? 'selected':'' }}>Rendah</option>
                    <option value="medium" {{ old('priority','medium')=='medium' ? 'selected':'' }}>Sedang</option>
                    <option value="high"   {{ old('priority')=='high'   ? 'selected':'' }}>Tinggi</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Deskripsi <span class="text-red-500">*</span></label>
            <textarea name="description" rows="4" required
                      class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none @error('description') border-red-400 @enderror"
                      placeholder="Jelaskan keluhan secara detail...">{{ old('description') }}</textarea>
            @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Foto Pendukung <span class="text-slate-400">(opsional)</span></label>
            <div class="border-2 border-dashed border-slate-300 rounded-xl p-6 text-center cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition-all"
                 onclick="document.getElementById('complaint-photos').click()">
                <svg class="w-8 h-8 text-slate-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="text-sm text-slate-600">Klik untuk upload foto</p>
                <p class="text-xs text-slate-400 mt-0.5">JPG, PNG — Maks. 2MB per foto</p>
            </div>
            <input type="file" id="complaint-photos" name="photos[]" multiple accept="image/*" class="hidden" onchange="previewComplaintPhotos(this)">
            <div id="complaint-preview" class="grid grid-cols-4 gap-2 mt-3"></div>
        </div>
    </div>

    <div class="flex gap-3">
        <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl transition-all shadow-lg shadow-blue-600/20">
            Simpan Keluhan
        </button>
        <a href="{{ route('complaints.index') }}" class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl transition-colors">
            Batal
        </a>
    </div>
</form>
</div>
@endsection

@push('scripts')
<script>
function previewComplaintPhotos(input) {
    const preview = document.getElementById('complaint-preview');
    preview.innerHTML = '';
    for (const file of input.files) {
        const reader = new FileReader();
        reader.onload = e => {
            const div = document.createElement('div');
            div.className = 'h-20 rounded-xl overflow-hidden bg-slate-100';
            div.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
            preview.appendChild(div);
        };
        reader.readAsDataURL(file);
    }
}
</script>
@endpush
