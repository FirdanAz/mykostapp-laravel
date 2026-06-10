<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoomRequest;
use App\Models\Facility;
use App\Models\Kost;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class RoomController extends Controller
{
    public function index(Request $request): View
    {
        $query = Room::with(['kost','facilities','activeTenant'])->latest();

        if ($request->search) {
            $query->where('number','like','%'.$request->search.'%');
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->floor) {
            $query->where('floor', $request->floor);
        }

        $rooms      = $query->paginate(12)->withQueryString();
        $facilities = Facility::orderBy('name')->get();
        $kost       = Kost::first();
        $floors     = Room::select('floor')->distinct()->orderBy('floor')->pluck('floor');

        return view('rooms.index', compact('rooms','facilities','kost','floors'));
    }

    public function create(): View
    {
        $facilities = Facility::orderBy('name')->get();
        $kost       = Kost::first();
        return view('rooms.create', compact('facilities','kost'));
    }

    public function store(RoomRequest $request): RedirectResponse
    {
        $kost = Kost::firstOrFail();
        $data = $request->except('photos','facilities');
        $data['kost_id'] = $kost->id;

        if ($request->hasFile('photos')) {
            $paths = [];
            foreach ($request->file('photos') as $photo) {
                $paths[] = $photo->store('rooms','public');
            }
            $data['photos'] = $paths;
        }

        $room = Room::create($data);

        if ($request->filled('facilities')) {
            $room->facilities()->sync($request->facilities);
        }

        return redirect()->route('rooms.index')
            ->with('success', "Kamar {$room->number} berhasil ditambahkan.");
    }

    public function show(Room $room): View
    {
        $room->load(['facilities','activeTenant','tenants' => fn($q) => $q->latest()]);
        return view('rooms.show', compact('room'));
    }

    public function edit(Room $room): View
    {
        $facilities = Facility::orderBy('name')->get();
        $room->load('facilities');
        return view('rooms.edit', compact('room','facilities'));
    }

    public function update(RoomRequest $request, Room $room): RedirectResponse
    {
        $data = $request->except('photos','facilities');

        if ($request->hasFile('photos')) {
            if ($room->photos) {
                foreach ($room->photos as $old) Storage::disk('public')->delete($old);
            }
            $paths = [];
            foreach ($request->file('photos') as $photo) {
                $paths[] = $photo->store('rooms','public');
            }
            $data['photos'] = $paths;
        }

        $room->update($data);

        if ($request->has('facilities')) {
            $room->facilities()->sync($request->facilities ?? []);
        }

        return redirect()->route('rooms.index')
            ->with('success', "Kamar {$room->number} berhasil diperbarui.");
    }

    public function destroy(Room $room): RedirectResponse
    {
        if ($room->status === 'occupied') {
            return back()->with('error', 'Kamar yang sedang terisi tidak dapat dihapus.');
        }

        if ($room->photos) {
            foreach ($room->photos as $p) Storage::disk('public')->delete($p);
        }

        $room->delete();
        return redirect()->route('rooms.index')
            ->with('success', "Kamar {$room->number} berhasil dihapus.");
    }
}
