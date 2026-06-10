@extends('layouts.app')
@section('title','Data Kos')
@section('page-title','Data Kos Saya')
@section('breadcrumb') <span class="mx-1">/</span> Data Kos @endsection

@section('content')
<div class="max-w-3xl space-y-5">

@if($kost->id)
{{-- Preview Card --}}
<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
    @if($kost->photos && count($kost->photos))
    <div class="h-48 bg-slate-100 overflow-hidden">
        <img src="{{ asset('storage/'.$kost->photos[0]) }}" alt="" class="w-full h-full object-cover">
    </div>
    @endif
    <div class="p-6">
        <div class="flex items-start gap-4">
            @if($kost->logo)
            <img src="{{ asset('storage/'.$kost->logo) }}" alt="" class="w-16 h-16 rounded-2xl object-cover ring-2 ring-slate-100 flex-shrink-0">
            @endif
            <div class="flex-1">
                <div class="flex items-center gap-2">
                    <h2 class="text-xl font-bold text-slate-900">{{ $kost->name }}</h2>
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $kost->is_published ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600' }}">
                        {{ $kost->is_published ? 'Dipublish' : 'Draft' }}
                    </span>
                    <span class="text-xs px-2 py-0.5 rounded-full bg-blue-100 text-blue-700">{{ $kost->type_label }}</span>
                </div>
                <p class="text-slate-500 text-sm mt-1">{{ $kost->address }}</p>
                <div class="flex flex-wrap gap-2 mt-3">
                    @if($kost->phone)
                    <span class="inline-flex items-center gap-1.5 text-xs text-slate-600 bg-slate-100 px-3 py-1.5 rounded-xl">
                        📞 {{ $kost->phone }}
                    </span>
                    @endif
                    @if($kost->email)
                    <span class="inline-flex items-center gap-1.5 text-xs text-slate-600 bg-slate-100 px-3 py-1.5 rounded-xl">
                        ✉️ {{ $kost->email }}
                    </span>
                    @endif
                </div>
            </div>
        </div>
        @if($kost->description)
        <p class="text-sm text-slate-600 mt-4 pt-4 border-t border-slate-100">{{ $kost->description }}</p>
        @endif
    </div>
</div>
@endif

{{-- Edit Form --}}
<form method="POST" action="{{ route('admin.kost.store') }}" enctype="multipart/form-data" class="space-y-5">
    @csrf

    <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-5">
        <div class="pb-3 border-b border-slate-100">
            <h3 class="font-semibold text-slate-800">{{ $kost->id ? 'Edit' : 'Tambah' }} Data Kos</h3>
            <p class="text-xs text-slate-400 mt-0.5">Informasi ini akan ditampilkan ke pencari kos</p>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Kos <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $kost->name) }}" required
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-400 @enderror"
                       placeholder="Kost Melati Indah">
                @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Tipe Kos <span class="text-red-500">*</span></label>
                <select name="type" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                    <option value="campur" {{ old('type', $kost->type) == 'campur' ? 'selected' : '' }}>Campur</option>
                    <option value="putra"  {{ old('type', $kost->type) == 'putra'  ? 'selected' : '' }}>Khusus Putra</option>
                    <option value="putri"  {{ old('type', $kost->type) == 'putri'  ? 'selected' : '' }}>Khusus Putri</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Kota</label>
                <input type="text" name="city" value="{{ old('city', $kost->city) }}"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Semarang">
            </div>

            <div class="col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Alamat Lengkap <span class="text-red-500">*</span></label>
                <textarea name="address" rows="2" required
                          class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('address') border-red-400 @enderror"
                          placeholder="Jl. Contoh No. 1, Kelurahan, Kecamatan, Kota">{{ old('address', $kost->address) }}</textarea>
                @error('address') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Deskripsi</label>
                <textarea name="description" rows="3"
                          class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                          placeholder="Deskripsi kos Anda...">{{ old('description', $kost->description) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Nomor Telepon</label>
                <input type="text" name="phone" value="{{ old('phone', $kost->phone) }}"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="08xxxxxxxxxx">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
                <input type="email" name="email" value="{{ old('email', $kost->email) }}"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="info@kostsaya.com">
            </div>
            <div class="col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Website</label>
                <input type="url" name="website" value="{{ old('website', $kost->website) }}"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="https://kostsaya.com">
            </div>

            {{-- Publikasi --}}
            <div class="col-span-2">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_published" value="1" {{ old('is_published', $kost->is_published) ? 'checked' : '' }}
                           class="w-4 h-4 text-blue-600 rounded border-slate-300">
                    <div>
                        <p class="text-sm font-medium text-slate-700">Tampilkan di halaman publik</p>
                        <p class="text-xs text-slate-400">Kos Anda dapat ditemukan oleh pencari kos</p>
                    </div>
                </label>
            </div>
        </div>
    </div>

    {{-- Logo --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-6">
        <h3 class="font-semibold text-slate-800 text-sm pb-3 border-b border-slate-100 mb-4">Logo Kos</h3>
        <div class="flex items-center gap-5">
            <div id="logo-preview-container"
                 class="w-20 h-20 rounded-2xl overflow-hidden flex items-center justify-center bg-slate-100 border-2 border-dashed border-slate-300 flex-shrink-0">
                @if($kost->logo)
                <img src="{{ asset('storage/'.$kost->logo) }}" alt="" class="w-full h-full object-cover">
                @else
                <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                @endif
            </div>
            <div>
                <input type="file" name="logo" id="logo-input" accept="image/*" class="hidden" onchange="previewLogo(this)">
                <button type="button" onclick="document.getElementById('logo-input').click()"
                        class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium rounded-xl transition-colors">
                    Upload Logo
                </button>
                <p class="text-xs text-slate-400 mt-1.5">JPG, PNG, SVG — Maks. 1MB</p>
            </div>
        </div>
    </div>

    {{-- Foto --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-6">
        <h3 class="font-semibold text-slate-800 text-sm pb-3 border-b border-slate-100 mb-4">Foto Kos</h3>
        @if($kost->photos && count($kost->photos))
        <div class="grid grid-cols-4 gap-3 mb-4">
            @foreach($kost->photos as $photo)
            <div class="relative h-24 rounded-xl overflow-hidden bg-slate-100">
                <img src="{{ asset('storage/'.$photo) }}" alt="" class="w-full h-full object-cover">
            </div>
            @endforeach
        </div>
        <p class="text-xs text-amber-600 bg-amber-50 border border-amber-200 rounded-xl px-3 py-2 mb-4">
            ⚠️ Upload foto baru akan menggantikan semua foto yang ada.
        </p>
        @endif
        <div class="border-2 border-dashed border-slate-300 rounded-xl p-8 text-center cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition-all"
             onclick="document.getElementById('kost-photos').click()">
            <svg class="w-10 h-10 text-slate-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p class="text-sm font-medium text-slate-600">Klik untuk upload foto kos</p>
            <p class="text-xs text-slate-400 mt-1">JPG, PNG, WEBP — Maks. 3MB per foto</p>
        </div>
        <input type="file" id="kost-photos" name="photos[]" multiple accept="image/*" class="hidden" onchange="previewKostPhotos(this)">
        <div id="kost-photo-preview" class="grid grid-cols-4 gap-3 mt-3"></div>
    </div>

    <div class="flex gap-3">
        <button type="submit"
                class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl transition-all shadow-lg shadow-blue-600/20">
            {{ $kost->id ? 'Simpan Perubahan' : 'Simpan Data Kos' }}
        </button>
        @if($kost->id)
        <a href="{{ route('public.kosts.show', $kost) }}" target="_blank"
           class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium rounded-xl transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
            </svg>
            Lihat Halaman Publik
        </a>
        @endif
    </div>
</form>
</div>
@endsection

@push('scripts')
<script>
function previewLogo(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('logo-preview-container').innerHTML =
                `<img src="${e.target.result}" class="w-full h-full object-cover">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
function previewKostPhotos(input) {
    const preview = document.getElementById('kost-photo-preview');
    preview.innerHTML = '';
    for (const file of input.files) {
        const reader = new FileReader();
        reader.onload = e => {
            const div = document.createElement('div');
            div.className = 'h-24 rounded-xl overflow-hidden bg-slate-100';
            div.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
            preview.appendChild(div);
        };
        reader.readAsDataURL(file);
    }
}
</script>
@endpush
