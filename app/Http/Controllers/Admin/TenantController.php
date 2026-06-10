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
    public function index(Request $request): View
    {
        $query = Tenant::with(['room'])->latest();

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name','like','%'.$request->search.'%')
                  ->orWhere('phone','like','%'.$request->search.'%')
                  ->orWhere('email','like','%'.$request->search.'%');
            });
        }
        if ($request->status) $query->where('status', $request->status);
        if ($request->gender) $query->where('gender', $request->gender);

        $tenants = $query->paginate(15)->withQueryString();
        $rooms   = Room::where('status','available')->orWhere('status','occupied')->orderBy('number')->get();

        return view('tenants.index', compact('tenants','rooms'));
    }

    public function create(): View
    {
        $rooms = Room::where('status','available')->orderBy('number')->get();
        return view('tenants.create', compact('rooms'));
    }

    public function store(TenantRequest $request): RedirectResponse
    {
        $data = $request->except('photo');

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('tenants','public');
        }

        $tenant = Tenant::create($data);

        // Mark room as occupied
        $tenant->room->update(['status' => 'occupied']);

        return redirect()->route('tenants.show', $tenant)
            ->with('success', "Penghuni {$tenant->name} berhasil ditambahkan.");
    }

    public function show(Tenant $tenant): View
    {
        $tenant->load(['room','invoices' => fn($q) => $q->latest(),'complaints' => fn($q) => $q->latest()]);
        return view('tenants.show', compact('tenant'));
    }

    public function edit(Tenant $tenant): View
    {
        $rooms = Room::where('status','available')
            ->orWhere('id', $tenant->room_id)
            ->orderBy('number')->get();
        return view('tenants.edit', compact('tenant','rooms'));
    }

    public function update(TenantRequest $request, Tenant $tenant): RedirectResponse
    {
        $data = $request->except('photo');

        if ($request->hasFile('photo')) {
            if ($tenant->photo) Storage::disk('public')->delete($tenant->photo);
            $data['photo'] = $request->file('photo')->store('tenants','public');
        }

        $oldRoomId = $tenant->room_id;
        $tenant->update($data);

        // Update room status if room changed
        if ($oldRoomId !== $tenant->room_id) {
            Room::find($oldRoomId)?->update(['status' => 'available']);
            $tenant->room->update(['status' => 'occupied']);
        }

        if ($request->status === 'inactive') {
            $tenant->room->update(['status' => 'available']);
        }

        return redirect()->route('tenants.show', $tenant)
            ->with('success', "Data penghuni {$tenant->name} berhasil diperbarui.");
    }

    public function destroy(Tenant $tenant): RedirectResponse
    {
        $room = $tenant->room;
        if ($tenant->photo) Storage::disk('public')->delete($tenant->photo);
        $tenant->delete();
        $room->update(['status' => 'available']);

        return redirect()->route('tenants.index')
            ->with('success', "Penghuni berhasil dihapus.");
    }
}
