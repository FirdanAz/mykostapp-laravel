<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TenantRequest;
use App\Models\Room;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TenantController extends Controller
{
    private function getKost()
    {
        $kost = auth()->user()->kost;
        if (!$kost) abort(403, 'Setup kos Anda terlebih dahulu.');
        return $kost;
    }

    public function index(Request $request): View
    {
        $kost  = $this->getKost();
        $query = Tenant::with(['room'])
            ->whereHas('room', fn($q) => $q->where('kost_id', $kost->id))
            ->latest();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->status) $query->where('status', $request->status);
        if ($request->gender) $query->where('gender', $request->gender);

        $tenants = $query->paginate(15)->withQueryString();
        $rooms   = Room::where('kost_id', $kost->id)
            ->whereIn('status', ['available', 'occupied'])
            ->orderBy('number')->get();

        return view('tenants.index', compact('tenants', 'rooms', 'kost'));
    }

    public function create(): View
    {
        $kost  = $this->getKost();
        $rooms = Room::where('kost_id', $kost->id)
            ->where('status', 'available')
            ->orderBy('number')->get();
        return view('tenants.create', compact('rooms', 'kost'));
    }

    public function store(TenantRequest $request): RedirectResponse
    {
        $kost = $this->getKost();

        // Pastikan room milik kos ini
        $room = Room::where('id', $request->room_id)
            ->where('kost_id', $kost->id)
            ->firstOrFail();

        $data = $request->except('photo');

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('tenants', 'public');
        }

        $tenant = Tenant::create($data);

        // Tandai kamar sebagai terisi
        $room->update(['status' => 'occupied']);

        return redirect()->route('admin.tenants.show', $tenant)
            ->with('success', "Penghuni {$tenant->name} berhasil ditambahkan.");
    }

    public function show(Tenant $tenant): View
    {
        $this->authorizeTenant($tenant);
        $tenant->load(['room', 'invoices' => fn($q) => $q->latest(), 'complaints' => fn($q) => $q->latest()]);
        return view('tenants.show', compact('tenant'));
    }

    public function edit(Tenant $tenant): View
    {
        $this->authorizeTenant($tenant);
        $kost  = $this->getKost();
        $rooms = Room::where('kost_id', $kost->id)
            ->where(fn($q) => $q->where('status', 'available')->orWhere('id', $tenant->room_id))
            ->orderBy('number')->get();
        return view('tenants.edit', compact('tenant', 'rooms'));
    }

    public function update(TenantRequest $request, Tenant $tenant): RedirectResponse
    {
        $this->authorizeTenant($tenant);
        $data = $request->except('photo');

        if ($request->hasFile('photo')) {
            if ($tenant->photo) Storage::disk('public')->delete($tenant->photo);
            $data['photo'] = $request->file('photo')->store('tenants', 'public');
        }

        $oldRoomId = $tenant->room_id;
        $tenant->update($data);

        // Update status kamar jika pindah kamar
        if ($oldRoomId !== $tenant->room_id) {
            Room::find($oldRoomId)?->update(['status' => 'available']);
            $tenant->fresh()->room->update(['status' => 'occupied']);
        }

        // Jika status inactive → kamar jadi available
        if ($request->status === 'inactive') {
            $tenant->fresh()->room?->update(['status' => 'available']);
        }

        return redirect()->route('admin.tenants.show', $tenant)
            ->with('success', "Data penghuni {$tenant->name} berhasil diperbarui.");
    }

    public function destroy(Tenant $tenant): RedirectResponse
    {
        $this->authorizeTenant($tenant);
        $room = $tenant->room;
        if ($tenant->photo) Storage::disk('public')->delete($tenant->photo);
        $tenant->delete();
        $room?->update(['status' => 'available']);

        return redirect()->route('admin.tenants.index')
            ->with('success', 'Penghuni berhasil dihapus.');
    }

    private function authorizeTenant(Tenant $tenant): void
    {
        $kost = auth()->user()->kost;
        if (!$kost || $tenant->room?->kost_id !== $kost->id) {
            abort(403, 'Anda tidak memiliki akses ke data penghuni ini.');
        }
    }
}
