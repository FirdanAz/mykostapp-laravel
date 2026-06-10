@extends('layouts.tenant')
@section('title', 'Buat Keluhan')
@section('page-title', 'Buat Keluhan')
@section('breadcrumb')
    <span class="mx-1">/</span> <a href="{{ route('tenant.complaints.index') }}" class="hover:text-slate-600">Keluhan</a>
    <span class="mx-1">/</span> Buat Baru
@endsection

@section('content')
<div class="max-w-2xl">
<form method="POST" action="{{ route('tenant.complaints.store') }}" enctype="multipart/form-data" class="space-y-5">
    @csrf

    <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4">

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">
                Judul Keluhan <span class="text-red-500">*</span>
            </label>
            <input type="text" name="title" value="{{ old('title') }}" required
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('title') border-red-400 @enderror"
                   placeholder="cth: AC kamar rusak, lampu mati, dll">
            @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Kategori <span class="text-red-500">*</span></label>
                <select name="category" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white @error('category') border-red-400 @enderror">
                    <option value="">Pilih kategori</option>
                    <option value="facility"    {{ old('category') == 'facility'    ? 'selected' : '' }}>Fasilitas</option>
                    <option value="security"    {{ old('category') == 'security'    ? 'selected' : '' }}>Keamanan</option>
                    <option value="cleanliness" {{ old('category') == 'cleanliness' ? 'selected' : '' }}>Kebersihan</option>
                    <option value="noise"       {{ old('category') == 'noise'       ? 'selected' : '' }}>Kebisingan</option>
                    <option value="other"       {{ old('category') == 'other'       ? 'selected' : '' }}>Lainnya</option>
                </select>
                @error('category') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Prioritas <span class="text-red-500">*</span></label>
                <select name="priority" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white @error('priority') border-red-400 @enderror">
                    <option value="">Pilih prioritas</option>
                    <option value="low"    {{ old('priority') == 'low'    ? 'selected' : '' }}>Rendah</option>
                    <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Sedang</option>
                    <option value="high"   {{ old('priority') == 'high'   ? 'selected' : '' }}>Tinggi</option>
                </select>
                @error('priority') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">
                Deskripsi Keluhan <span class="text-red-500">*</span>
            </label>
            <textarea name="description" rows="4" required
                      class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 resize-none @error('description') border-red-400 @enderror"
                      placeholder="Jelaskan masalah yang Anda hadapi secara detail...">{{ old('description') }}</textarea>
            @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">
                Foto Pendukung <span class="text-slate-400 font-normal">(opsional)</span>
            </label>
            <div class="border-2 border-dashed border-slate-300 rounded-xl p-4 text-center hover:border-emerald-400 transition-colors cursor-pointer"
                 onclick="document.getElementById('complaint-photos').click()">
                <svg class="w-8 h-8 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="text-sm text-slate-400">Foto bukti masalah (maks. 3 foto)</p>
            </div>
            <input type="file" id="complaint-photos" name="photos[]" multiple accept="image/*" class="hidden">
            <div id="photo-preview" class="flex flex-wrap gap-2 mt-2"></div>
        </div>
    </div>

    <div class="flex gap-3">
        <button type="submit"
                class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 px-4 rounded-xl transition-all shadow-lg shadow-emerald-600/20 flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
            </svg>
            Kirim Keluhan
        </button>
        <a href="{{ route('tenant.complaints.index') }}"
           class="px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium rounded-xl transition-colors text-sm">
            Batal
        </a>
    </div>
</form>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('complaint-photos').addEventListener('change', function(e) {
    const preview = document.getElementById('photo-preview');
    preview.innerHTML = '';
    Array.from(e.target.files).slice(0, 3).forEach(file => {
        const reader = new FileReader();
        reader.onload = ev => {
            const div = document.createElement('div');
            div.className = 'w-20 h-20 rounded-lg overflow-hidden border border-slate-200';
            div.innerHTML = `<img src="${ev.target.result}" class="w-full h-full object-cover">`;
            preview.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
});
</script>
@endpush
