<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoomRequest;
use App\Models\Facility;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class RoomController extends Controller
{
    private function getKost()
    {
        $kost = auth()->user()->kost;
        if (!$kost) {
            abort(403, 'Anda belum memiliki kos. Silakan setup kos terlebih dahulu.');
        }
        return $kost;
    }

    public function index(Request $request): View
    {
        $kost  = $this->getKost();
        $query = Room::with(['facilities', 'activeTenant'])
            ->where('kost_id', $kost->id)
            ->latest();

        if ($request->search) {
            $query->where('number', 'like', '%' . $request->search . '%');
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->floor) {
            $query->where('floor', $request->floor);
        }

        $rooms      = $query->paginate(12)->withQueryString();
        $facilities = Facility::orderBy('name')->get();
        $floors     = Room::where('kost_id', $kost->id)->select('floor')->distinct()->orderBy('floor')->pluck('floor');

        return view('rooms.index', compact('rooms', 'facilities', 'kost', 'floors'));
    }

    public function create(): View
    {
        $kost       = $this->getKost();
        $facilities = Facility::orderBy('name')->get();
        return view('rooms.create', compact('facilities', 'kost'));
    }

    public function store(RoomRequest $request): RedirectResponse
    {
        $kost = $this->getKost();
        $data = $request->except('photos', 'facilities');
        $data['kost_id'] = $kost->id;

        if ($request->hasFile('photos')) {
            $paths = [];
            foreach ($request->file('photos') as $photo) {
                $paths[] = $photo->store('rooms', 'public');
            }
            $data['photos'] = $paths;
        }

        $room = Room::create($data);

        if ($request->filled('facilities')) {
            $room->facilities()->sync($request->facilities);
        }

        return redirect()->route('admin.rooms.index')
            ->with('success', "Kamar {$room->number} berhasil ditambahkan.");
    }

    public function show(Room $room): View
    {
        $this->authorizeRoom($room);
        $room->load(['facilities', 'activeTenant', 'tenants' => fn($q) => $q->latest()]);
        return view('rooms.show', compact('room'));
    }

    public function edit(Room $room): View
    {
        $this->authorizeRoom($room);
        $facilities = Facility::orderBy('name')->get();
        $room->load('facilities');
        return view('rooms.edit', compact('room', 'facilities'));
    }

    public function update(RoomRequest $request, Room $room): RedirectResponse
    {
        $this->authorizeRoom($room);
        $data = $request->except('photos', 'facilities');

        if ($request->hasFile('photos')) {
            if ($room->photos) {
                foreach ($room->photos as $old) Storage::disk('public')->delete($old);
            }
            $paths = [];
            foreach ($request->file('photos') as $photo) {
                $paths[] = $photo->store('rooms', 'public');
            }
            $data['photos'] = $paths;
        }

        $room->update($data);

        if ($request->has('facilities')) {
            $room->facilities()->sync($request->facilities ?? []);
        }

        return redirect()->route('admin.rooms.index')
            ->with('success', "Kamar {$room->number} berhasil diperbarui.");
    }

    public function destroy(Room $room): RedirectResponse
    {
        $this->authorizeRoom($room);

        if ($room->status === 'occupied') {
            return back()->with('error', 'Kamar yang sedang terisi tidak dapat dihapus.');
        }

        if ($room->photos) {
            foreach ($room->photos as $p) Storage::disk('public')->delete($p);
        }

        $room->delete();
        return redirect()->route('admin.rooms.index')
            ->with('success', "Kamar {$room->number} berhasil dihapus.");
    }

    /** Pastikan room milik kos admin yg login */
    private function authorizeRoom(Room $room): void
    {
        $kost = auth()->user()->kost;
        if (!$kost || $room->kost_id !== $kost->id) {
            abort(403, 'Anda tidak memiliki akses ke kamar ini.');
        }
    }
}
