@extends('layouts.app')
@section('title','Data Kost')
@section('page-title','Data Kost')
@section('breadcrumb') <span class="mx-1">/</span> Data Kost @endsection

@section('content')
<div class="max-w-3xl space-y-5">

@if($kost->id)
{{-- Current Kost Preview --}}
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
            <div>
                <h2 class="text-xl font-bold text-slate-900">{{ $kost->name }}</h2>
                <p class="text-slate-500 text-sm mt-1">{{ $kost->address }}</p>
                <div class="flex flex-wrap gap-3 mt-3">
                    @if($kost->phone)
                    <a href="tel:{{ $kost->phone }}" class="inline-flex items-center gap-1.5 text-xs text-slate-600 bg-slate-100 px-3 py-1.5 rounded-xl hover:bg-slate-200 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        {{ $kost->phone }}
                    </a>
                    @endif
                    @if($kost->email)
                    <span class="inline-flex items-center gap-1.5 text-xs text-slate-600 bg-slate-100 px-3 py-1.5 rounded-xl">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        {{ $kost->email }}
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
            <h3 class="font-semibold text-slate-800">{{ $kost->id ? 'Edit' : 'Tambah' }} Data Kost</h3>
            <p class="text-xs text-slate-400 mt-0.5">Informasi ini akan ditampilkan di seluruh aplikasi</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Kost <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name', $kost->name) }}" required
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-400 @enderror"
                   placeholder="Kost Melati Indah">
            @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Deskripsi</label>
            <textarea name="description" rows="3"
                      class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                      placeholder="Deskripsi singkat tentang kost Anda...">{{ old('description', $kost->description) }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Alamat Lengkap <span class="text-red-500">*</span></label>
            <textarea name="address" rows="2" required
                      class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none @error('address') border-red-400 @enderror"
                      placeholder="Jl. Contoh No. 1, Kelurahan, Kecamatan, Kota, Provinsi">{{ old('address', $kost->address) }}</textarea>
            @error('address') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
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
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Website</label>
                <input type="url" name="website" value="{{ old('website', $kost->website) }}"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="https://kostsaya.com">
            </div>
        </div>
    </div>

    {{-- Logo Upload --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-6">
        <h3 class="font-semibold text-slate-800 text-sm pb-3 border-b border-slate-100 mb-4">Logo Kost</h3>
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
                <p class="text-xs text-slate-400">Rekomendasi: 200×200px</p>
            </div>
        </div>
    </div>

    {{-- Photos Upload --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-6">
        <h3 class="font-semibold text-slate-800 text-sm pb-3 border-b border-slate-100 mb-4">Foto Kost</h3>

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

        <div id="kost-photo-drop"
             class="border-2 border-dashed border-slate-300 rounded-xl p-8 text-center cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition-all"
             onclick="document.getElementById('kost-photos').click()">
            <svg class="w-10 h-10 text-slate-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p class="text-sm font-medium text-slate-600">Klik untuk upload foto kost</p>
            <p class="text-xs text-slate-400 mt-1">JPG, PNG, WEBP — Maks. 3MB per foto, pilih beberapa sekaligus</p>
        </div>
        <input type="file" id="kost-photos" name="photos[]" multiple accept="image/*" class="hidden" onchange="previewKostPhotos(this)">
        <div id="kost-photo-preview" class="grid grid-cols-4 gap-3 mt-3"></div>
    </div>

    <div class="flex gap-3">
        <button type="submit"
                class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl transition-all shadow-lg shadow-blue-600/20">
            {{ $kost->id ? 'Simpan Perubahan' : 'Simpan Data Kost' }}
        </button>
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
            const c = document.getElementById('logo-preview-container');
            c.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
            c.classList.remove('border-dashed','border-slate-300');
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
