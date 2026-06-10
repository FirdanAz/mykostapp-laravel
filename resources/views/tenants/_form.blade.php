{{-- resources/views/tenants/_form.blade.php --}}
<div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-5">
    <h3 class="font-semibold text-slate-800 text-sm pb-3 border-b border-slate-100">Data Penghuni</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="sm:col-span-2">
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name', $tenant?->name) }}" required
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-400 @enderror"
                   placeholder="Nama penghuni">
            @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Nomor HP <span class="text-red-500">*</span></label>
            <input type="text" name="phone" value="{{ old('phone', $tenant?->phone) }}" required
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('phone') border-red-400 @enderror"
                   placeholder="08xxxxxxxxxx">
            @error('phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
            <input type="email" name="email" value="{{ old('email', $tenant?->email) }}"
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                   placeholder="email@contoh.com">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Jenis Kelamin <span class="text-red-500">*</span></label>
            <select name="gender" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="male"   {{ old('gender', $tenant?->gender) == 'male'   ? 'selected':'' }}>Laki-laki</option>
                <option value="female" {{ old('gender', $tenant?->gender) == 'female' ? 'selected':'' }}>Perempuan</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Kamar <span class="text-red-500">*</span></label>
            <select name="room_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 @error('room_id') border-red-400 @enderror">
                <option value="">-- Pilih Kamar --</option>
                @foreach($rooms as $room)
                <option value="{{ $room->id }}"
                        {{ old('room_id', $tenant?->room_id) == $room->id ? 'selected' : '' }}
                        {{ request('room_id') == $room->id ? 'selected' : '' }}>
                    Kamar {{ $room->number }} (Lt.{{ $room->floor }}) — {{ $room->formatted_price }}
                    {{ $room->status !== 'available' && $room->id !== $tenant?->room_id ? '(Terisi)' : '' }}
                </option>
                @endforeach
            </select>
            @error('room_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Tanggal Masuk <span class="text-red-500">*</span></label>
            <input type="date" name="start_date" value="{{ old('start_date', $tenant?->start_date?->format('Y-m-d')) }}" required
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('start_date') border-red-400 @enderror">
            @error('start_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Tanggal Keluar <span class="text-slate-400">(opsional)</span></label>
            <input type="date" name="end_date" value="{{ old('end_date', $tenant?->end_date?->format('Y-m-d')) }}"
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Status</label>
            <select name="status" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="active"   {{ old('status', $tenant?->status ?? 'active') == 'active'   ? 'selected':'' }}>Aktif</option>
                <option value="inactive" {{ old('status', $tenant?->status) == 'inactive' ? 'selected':'' }}>Tidak Aktif</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">No. KTP / Identitas</label>
            <input type="text" name="id_card" value="{{ old('id_card', $tenant?->id_card) }}"
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                   placeholder="3300xxxxxxxxxxxxxx">
        </div>
        <div class="sm:col-span-2">
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Alamat Asal</label>
            <textarea name="address" rows="2"
                      class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                      placeholder="Alamat lengkap penghuni">{{ old('address', $tenant?->address) }}</textarea>
        </div>
        <div class="sm:col-span-2">
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Catatan</label>
            <textarea name="notes" rows="2"
                      class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                      placeholder="Catatan tambahan...">{{ old('notes', $tenant?->notes) }}</textarea>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl border border-slate-200 p-6">
    <h3 class="font-semibold text-slate-800 text-sm pb-3 border-b border-slate-100 mb-4">Foto Penghuni</h3>
    @if($tenant?->photo)
    <div class="mb-4 flex items-center gap-4">
        <img src="{{ $tenant->photo_url }}" alt="" class="w-20 h-20 rounded-2xl object-cover ring-2 ring-slate-200">
        <div>
            <p class="text-sm font-medium text-slate-700">Foto saat ini</p>
            <p class="text-xs text-slate-400 mt-0.5">Upload foto baru untuk mengganti</p>
        </div>
    </div>
    @endif
    <div class="flex items-center gap-4">
        <div id="photo-preview-container" class="w-20 h-20 bg-slate-100 rounded-2xl overflow-hidden flex items-center justify-center border-2 border-dashed border-slate-300">
            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
        </div>
        <div>
            <input type="file" name="photo" id="tenant-photo" accept="image/*" class="hidden" onchange="previewTenantPhoto(this)">
            <button type="button" onclick="document.getElementById('tenant-photo').click()"
                    class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium rounded-xl transition-colors">
                Pilih Foto
            </button>
            <p class="text-xs text-slate-400 mt-1.5">JPG, PNG — Maks. 2MB</p>
        </div>
    </div>
</div>

<script>
function previewTenantPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const container = document.getElementById('photo-preview-container');
            container.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
            container.classList.remove('border-dashed','border-slate-300');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
