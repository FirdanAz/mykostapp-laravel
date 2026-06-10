@extends('layouts.app')
@section('title','Profil Saya')
@section('page-title','Profil Saya')
@section('breadcrumb') <span class="mx-1">/</span> Profil @endsection

@section('content')
<div class="max-w-2xl space-y-5">

{{-- Profile Card --}}
<div class="bg-white rounded-2xl border border-slate-200 p-6">
    <div class="flex items-center gap-5 pb-6 border-b border-slate-100 mb-6">
        <div class="relative">
            <img src="{{ auth()->user()->avatar_url }}" alt=""
                 id="avatar-preview"
                 class="w-20 h-20 rounded-2xl object-cover ring-4 ring-slate-100">
        </div>
        <div>
            <h2 class="text-xl font-bold text-slate-900">{{ $user->name }}</h2>
            <p class="text-slate-500 text-sm">{{ $user->email }}</p>
            <span class="inline-flex items-center gap-1.5 mt-2 text-xs font-semibold px-2.5 py-1 rounded-full bg-blue-100 text-blue-700">
                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                {{ ucfirst($user->role) }}
            </span>
        </div>
    </div>

    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-5">
        @csrf @method('PATCH')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-400 @enderror">
                @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-400 @enderror">
                @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Nomor HP</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="08xxxxxxxxxx">
            </div>
        </div>

        {{-- Avatar Upload --}}
        <div class="pt-4 border-t border-slate-100">
            <label class="block text-sm font-medium text-slate-700 mb-3">Foto Profil</label>
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl overflow-hidden bg-slate-100 flex-shrink-0 ring-2 ring-slate-200">
                    <img id="avatar-new-preview" src="{{ auth()->user()->avatar_url }}" alt="" class="w-full h-full object-cover">
                </div>
                <div>
                    <input type="file" name="avatar" id="avatar-input" accept="image/*" class="hidden"
                           onchange="previewAvatar(this)">
                    <button type="button" onclick="document.getElementById('avatar-input').click()"
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium rounded-xl transition-colors">
                        Ganti Foto
                    </button>
                    <p class="text-xs text-slate-400 mt-1.5">JPG, PNG, WEBP — Maks. 2MB</p>
                    @error('avatar') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="pt-2 flex gap-3">
            <button type="submit"
                    class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl transition-all shadow-lg shadow-blue-600/20">
                Simpan Profil
            </button>
        </div>
    </form>
</div>

{{-- Change Password --}}
<div class="bg-white rounded-2xl border border-slate-200 p-6">
    <div class="pb-4 border-b border-slate-100 mb-5">
        <h3 class="font-semibold text-slate-800">Ubah Password</h3>
        <p class="text-xs text-slate-400 mt-0.5">Gunakan password yang kuat dan unik</p>
    </div>

    <form method="POST" action="{{ route('profile.password.update') }}" class="space-y-5">
        @csrf @method('PUT')

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Password Saat Ini <span class="text-red-500">*</span></label>
            <input type="password" name="current_password" required
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('current_password') border-red-400 @enderror"
                   placeholder="••••••••">
            @error('current_password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Password Baru <span class="text-red-500">*</span></label>
                <input type="password" name="password" required
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-400 @enderror"
                       placeholder="Min. 8 karakter">
                @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Konfirmasi Password Baru <span class="text-red-500">*</span></label>
                <input type="password" name="password_confirmation" required
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Ulangi password baru">
            </div>
        </div>

        <button type="submit"
                class="px-6 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-semibold text-sm rounded-xl transition-all">
            Ubah Password
        </button>
    </form>
</div>

{{-- Account Info --}}
<div class="bg-white rounded-2xl border border-slate-200 p-6">
    <h3 class="font-semibold text-slate-800 pb-4 border-b border-slate-100 mb-4">Informasi Akun</h3>
    <div class="grid grid-cols-2 gap-4 text-sm">
        <div class="p-3 bg-slate-50 rounded-xl">
            <p class="text-xs text-slate-400 mb-1">Role</p>
            <p class="font-semibold text-slate-700 capitalize">{{ $user->role }}</p>
        </div>
        <div class="p-3 bg-slate-50 rounded-xl">
            <p class="text-xs text-slate-400 mb-1">Bergabung Sejak</p>
            <p class="font-semibold text-slate-700">{{ $user->created_at->format('d M Y') }}</p>
        </div>
        <div class="p-3 bg-slate-50 rounded-xl">
            <p class="text-xs text-slate-400 mb-1">Email Verified</p>
            <p class="font-semibold {{ $user->email_verified_at ? 'text-green-600' : 'text-amber-600' }}">
                {{ $user->email_verified_at ? 'Terverifikasi' : 'Belum Diverifikasi' }}
            </p>
        </div>
        <div class="p-3 bg-slate-50 rounded-xl">
            <p class="text-xs text-slate-400 mb-1">Login Terakhir</p>
            <p class="font-semibold text-slate-700">{{ now()->format('d M Y H:i') }}</p>
        </div>
    </div>
</div>

</div>
@endsection

@push('scripts')
<script>
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('avatar-new-preview').src = e.target.result;
            document.getElementById('avatar-preview').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
