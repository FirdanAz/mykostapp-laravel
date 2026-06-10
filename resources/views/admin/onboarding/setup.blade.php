@extends('layouts.auth')
@section('title','Setup Kos — Onboarding')
@section('content')

<div class="mb-6">
    <div class="flex items-center gap-3 mb-3">
        <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
        </div>
        <div>
            <h2 class="text-xl font-bold text-slate-900">Setup Data Kos</h2>
            <p class="text-slate-500 text-sm">Langkah terakhir sebelum mulai!</p>
        </div>
    </div>

    {{-- Progress indicator --}}
    <div class="flex items-center gap-2 text-xs text-slate-500 bg-slate-50 rounded-xl px-4 py-2">
        <span class="flex items-center gap-1 text-green-600 font-medium">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            Akun dibuat
        </span>
        <svg class="w-3 h-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="font-medium text-blue-600">Setup Kos</span>
        <svg class="w-3 h-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-slate-400">Dashboard</span>
    </div>
</div>

@if(session('info'))
<div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-xl text-sm text-blue-700">
    {{ session('info') }}
</div>
@endif

<form method="POST" action="{{ route('admin.kost.setup.store') }}" enctype="multipart/form-data" class="space-y-4">
    @csrf

    <div class="grid grid-cols-2 gap-4">

        {{-- Nama Kos --}}
        <div class="col-span-2">
            <label class="block text-sm font-medium text-slate-700 mb-1.5">
                Nama Kos <span class="text-red-500">*</span>
            </label>
            <input type="text" name="name" value="{{ old('name') }}" required
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-400 @enderror"
                   placeholder="cth: Kos Melati Indah">
            @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Tipe Kos --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">
                Tipe Kos <span class="text-red-500">*</span>
            </label>
            <select name="type" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white @error('type') border-red-400 @enderror">
                <option value="campur" {{ old('type') == 'campur' ? 'selected' : '' }}>Campur (Putra & Putri)</option>
                <option value="putra"  {{ old('type') == 'putra'  ? 'selected' : '' }}>Khusus Putra</option>
                <option value="putri"  {{ old('type') == 'putri'  ? 'selected' : '' }}>Khusus Putri</option>
            </select>
            @error('type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Kota --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Kota</label>
            <input type="text" name="city" value="{{ old('city') }}"
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                   placeholder="cth: Semarang">
        </div>

        {{-- Alamat --}}
        <div class="col-span-2">
            <label class="block text-sm font-medium text-slate-700 mb-1.5">
                Alamat Lengkap <span class="text-red-500">*</span>
            </label>
            <textarea name="address" rows="2" required
                      class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('address') border-red-400 @enderror"
                      placeholder="Jl. Contoh No. 12, Kelurahan, Kecamatan, Kota">{{ old('address') }}</textarea>
            @error('address') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Deskripsi --}}
        <div class="col-span-2">
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Deskripsi Kos</label>
            <textarea name="description" rows="3"
                      class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      placeholder="Ceritakan tentang kos Anda, fasilitas umum, lokasi strategis, dll...">{{ old('description') }}</textarea>
        </div>

        {{-- No. HP --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">No. HP / WA Kos</label>
            <input type="text" name="phone" value="{{ old('phone', auth()->user()->phone) }}"
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                   placeholder="08xxxxxxxxxx">
        </div>

        {{-- Email Kos --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Email Kos</label>
            <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}"
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                   placeholder="info@kosanda.com">
        </div>

        {{-- Foto Kos --}}
        <div class="col-span-2">
            <label class="block text-sm font-medium text-slate-700 mb-1.5">
                Foto Kos <span class="text-slate-400 font-normal">(opsional, maks. 5 foto, 3MB/foto)</span>
            </label>
            <div class="border-2 border-dashed border-slate-300 rounded-xl p-4 text-center hover:border-blue-400 transition-colors cursor-pointer"
                 onclick="document.getElementById('photos-input').click()">
                <svg class="w-8 h-8 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="text-sm text-slate-500">Klik untuk upload foto atau drag & drop</p>
                <p class="text-xs text-slate-400 mt-1">JPG, PNG, WebP — Maks. 3 MB/foto</p>
            </div>
            <input type="file" id="photos-input" name="photos[]" multiple accept="image/*" class="hidden">
            <div id="photo-preview" class="flex flex-wrap gap-2 mt-2"></div>
            @error('photos.*') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="flex gap-3 pt-2">
        <button type="submit"
                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-4 rounded-xl transition-all text-sm shadow-lg shadow-blue-600/20 flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Simpan & Mulai Kelola Kos
        </button>
    </div>

    <form method="POST" action="{{ route('logout') }}" class="inline">
        @csrf
        <button type="submit" class="w-full text-center text-sm text-slate-400 hover:text-slate-600 transition-colors py-1">
            Logout
        </button>
    </form>
</form>

@push('scripts')
<script>
document.getElementById('photos-input').addEventListener('change', function(e) {
    const preview = document.getElementById('photo-preview');
    preview.innerHTML = '';
    Array.from(e.target.files).slice(0, 5).forEach(file => {
        const reader = new FileReader();
        reader.onload = ev => {
            const div = document.createElement('div');
            div.className = 'relative';
            div.innerHTML = `<img src="${ev.target.result}" class="w-20 h-20 object-cover rounded-lg border border-slate-200">`;
            preview.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
});
</script>
@endpush
@endsection
