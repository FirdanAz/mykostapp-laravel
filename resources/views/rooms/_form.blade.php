{{-- resources/views/rooms/_form.blade.php --}}
<div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-5">
    <h3 class="font-semibold text-slate-800 text-sm pb-3 border-b border-slate-100">Informasi Kamar</h3>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Nomor Kamar <span class="text-red-500">*</span></label>
            <input type="text" name="number" value="{{ old('number', $room?->number) }}" required
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('number') border-red-400 @enderror"
                   placeholder="Contoh: 101">
            @error('number') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Lantai <span class="text-red-500">*</span></label>
            <input type="number" name="floor" value="{{ old('floor', $room?->floor ?? 1) }}" required min="1"
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('floor') border-red-400 @enderror"
                   placeholder="1">
            @error('floor') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Harga / Bulan (Rp) <span class="text-red-500">*</span></label>
            <input type="number" name="price" value="{{ old('price', $room?->price) }}" required min="0" step="1000"
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('price') border-red-400 @enderror"
                   placeholder="800000">
            @error('price') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Status <span class="text-red-500">*</span></label>
            <select name="status" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white @error('status') border-red-400 @enderror">
                <option value="available"   {{ old('status', $room?->status) == 'available'   ? 'selected':'' }}>Tersedia</option>
                <option value="occupied"    {{ old('status', $room?->status) == 'occupied'    ? 'selected':'' }}>Terisi</option>
                <option value="maintenance" {{ old('status', $room?->status) == 'maintenance' ? 'selected':'' }}>Maintenance</option>
            </select>
            @error('status') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Deskripsi</label>
        <textarea name="description" rows="3"
                  class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                  placeholder="Deskripsi kamar...">{{ old('description', $room?->description) }}</textarea>
    </div>
</div>

{{-- Facilities --}}
<div class="bg-white rounded-2xl border border-slate-200 p-6">
    <h3 class="font-semibold text-slate-800 text-sm pb-3 border-b border-slate-100 mb-4">Fasilitas</h3>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
        @foreach($facilities as $facility)
        <label class="flex items-center gap-2 cursor-pointer group">
            <input type="checkbox" name="facilities[]" value="{{ $facility->id }}"
                   {{ in_array($facility->id, old('facilities', $room?->facilities->pluck('id')->toArray() ?? [])) ? 'checked' : '' }}
                   class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
            <span class="text-sm text-slate-700 group-hover:text-slate-900">{{ $facility->name }}</span>
        </label>
        @endforeach
    </div>
</div>

{{-- Photos --}}
<div class="bg-white rounded-2xl border border-slate-200 p-6">
    <h3 class="font-semibold text-slate-800 text-sm pb-3 border-b border-slate-100 mb-4">Foto Kamar</h3>

    @if($room && $room->photos)
    <div class="grid grid-cols-4 gap-3 mb-4">
        @foreach($room->photos as $photo)
        <div class="relative rounded-xl overflow-hidden h-24 bg-slate-100">
            <img src="{{ asset('storage/'.$photo) }}" alt="" class="w-full h-full object-cover">
        </div>
        @endforeach
    </div>
    <p class="text-xs text-slate-500 mb-3">Upload foto baru untuk mengganti foto yang ada.</p>
    @endif

    <div id="photo-drop-area"
         class="border-2 border-dashed border-slate-300 rounded-xl p-8 text-center cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition-all"
         onclick="document.getElementById('room-photos').click()">
        <svg class="w-8 h-8 text-slate-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <p class="text-sm font-medium text-slate-600">Klik untuk upload foto</p>
        <p class="text-xs text-slate-400 mt-1">JPG, PNG, WEBP — Maks. 2MB per foto</p>
    </div>
    <input type="file" id="room-photos" name="photos[]" multiple accept="image/*" class="hidden" onchange="previewPhotos(this)">
    <div id="photo-preview" class="grid grid-cols-4 gap-3 mt-3"></div>
</div>

<script>
function previewPhotos(input) {
    const preview = document.getElementById('photo-preview');
    preview.innerHTML = '';
    for (const file of input.files) {
        const reader = new FileReader();
        reader.onload = e => {
            const div = document.createElement('div');
            div.className = 'relative rounded-xl overflow-hidden h-24 bg-slate-100';
            div.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
            preview.appendChild(div);
        };
        reader.readAsDataURL(file);
    }
}
</script>
