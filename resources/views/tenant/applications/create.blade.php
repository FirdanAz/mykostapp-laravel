@extends('layouts.tenant')
@section('title', 'Form Pengajuan Sewa Kamar')
@section('page-title', 'Form Pengajuan Sewa')

@section('breadcrumb')
<span class="mx-1">/</span>
<a href="{{ route('tenant.applications.index') }}" class="hover:text-slate-600">Pengajuan</a>
<span class="mx-1">/</span>
Baru
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Form input berkas sewa --}}
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
            <div class="border-b border-slate-100 pb-4 mb-5">
                <h3 class="text-lg font-bold text-slate-800">Formulir Persyaratan & Berkas Sewa</h3>
                <p class="text-xs text-slate-500 mt-1">Harap isi data dengan lengkap & valid. Identitas Anda akan diverifikasi oleh pemilik kos.</p>
            </div>

            <form method="POST" action="{{ route('tenant.applications.store') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf
                <input type="hidden" name="room_id" value="{{ $room->id }}">

                {{-- NIK & Foto KTP --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nomor KTP (NIK) <span class="text-red-500">*</span></label>
                        <input type="text" name="id_card" value="{{ old('id_card') }}" required
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('id_card') border-red-400 @enderror"
                               placeholder="16 digit NIK Anda">
                        @error('id_card') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Foto/Scan KTP <span class="text-red-500">*</span></label>
                        <input type="file" name="id_card_photo" required
                               class="w-full px-3 py-1.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('id_card_photo') border-red-400 @enderror">
                        <span class="text-[10px] text-slate-400 block mt-1">Maks. file size 2MB (JPG, PNG, WEBP)</span>
                        @error('id_card_photo') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Data Profil --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm bg-slate-50 cursor-not-allowed focus:outline-none" readonly>
                        @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Alamat Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm bg-slate-50 cursor-not-allowed focus:outline-none" readonly>
                        @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nomor HP <span class="text-red-500">*</span></label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" required
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('phone') border-red-400 @enderror"
                               placeholder="08xxxxxxxxxx">
                        @error('phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jenis Kelamin <span class="text-red-500">*</span></label>
                        <select name="gender" required
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('gender') border-red-400 @enderror">
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('gender') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Alamat Asal <span class="text-red-500">*</span></label>
                    <textarea name="address" rows="3" required
                              class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('address') border-red-400 @enderror"
                              placeholder="Alamat asal lengkap berdasarkan KTP...">{{ old('address') }}</textarea>
                    @error('address') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Tanggal Masuk & Durasi --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tanggal Mulai Sewa <span class="text-red-500">*</span></label>
                        <input type="date" name="start_date" value="{{ old('start_date') }}" required
                               min="{{ date('Y-m-d') }}"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('start_date') border-red-400 @enderror">
                        @error('start_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Durasi Sewa Kamar <span class="text-red-500">*</span></label>
                        <select name="duration_months" required
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('duration_months') border-red-400 @enderror">
                            <option value="">Pilih Durasi Kontrak</option>
                            <option value="1" {{ old('duration_months') === '1' ? 'selected' : '' }}>1 Bulan</option>
                            <option value="3" {{ old('duration_months') === '3' ? 'selected' : '' }}>3 Bulan</option>
                            <option value="6" {{ old('duration_months') === '6' ? 'selected' : '' }}>6 Bulan</option>
                            <option value="12" {{ old('duration_months') === '12' ? 'selected' : '' }}>12 Bulan (1 Tahun)</option>
                        </select>
                        @error('duration_months') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Catatan Tambahan ke Pemilik <span class="text-slate-400 font-normal">(opsional)</span></label>
                    <textarea name="notes" rows="2"
                              class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Misal: Saya bawa hewan peliharaan, atau punya kendaraan bermotor tambahan...">{{ old('notes') }}</textarea>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('public.kosts.show', $room->kost_id) }}"
                       class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm rounded-xl transition-colors shadow-lg shadow-emerald-600/10">
                        Kirim Pengajuan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Detail Kamar Card --}}
    <div class="space-y-6">
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-4">
            <h4 class="text-sm font-bold text-slate-800">Detail Kamar Pilihan</h4>

            @if($room->first_photo)
            <img src="{{ $room->first_photo }}" alt="" class="w-full h-40 object-cover rounded-xl">
            @else
            <div class="w-full h-40 bg-slate-100 rounded-xl flex items-center justify-center">
                <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            @endif

            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-700">{{ $room->kost->type_label }}</span>
                    <span class="text-xs text-slate-400">Lantai {{ $room->floor }}</span>
                </div>
                <h3 class="text-lg font-bold text-slate-900">{{ $room->kost->name }}</h3>
                <p class="text-sm text-slate-800 font-semibold">Kamar {{ $room->number }}</p>
                @if($room->kost->address)
                <p class="text-xs text-slate-500 leading-relaxed">{{ $room->kost->address }}</p>
                @endif
            </div>

            <div class="border-t border-slate-100 pt-4 flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500">Tarif Bulanan:</span>
                <span class="text-base font-extrabold text-blue-600">{{ $room->formatted_price }}<span class="text-xs text-slate-400 font-normal">/bln</span></span>
            </div>
        </div>

        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5">
            <h4 class="text-xs font-bold text-amber-800 uppercase tracking-wider mb-2">Penting</h4>
            <ul class="text-xs text-amber-700 space-y-1.5 list-disc pl-4 leading-relaxed">
                <li>Harap mengunggah scan/foto KTP asli Anda dengan jelas dan tidak buram.</li>
                <li>Setelah mengirimkan pengajuan, harap menunggu konfirmasi pemilik kos. Anda akan mendapatkan notifikasi.</li>
                <li>Setelah disetujui, Anda wajib melakukan pembayaran bulan pertama untuk mengonfirmasi pemesanan sewa.</li>
            </ul>
        </div>
    </div>

</div>
@endsection
