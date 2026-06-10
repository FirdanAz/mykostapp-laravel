<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\ComplaintReply;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ComplaintController extends Controller
{
    private function getTenantProfile()
    {
        $profile = auth()->user()->tenantProfile;
        if (!$profile) abort(403, 'Anda belum terdaftar sebagai penghuni kos.');
        return $profile;
    }

    public function index(): View
    {
        $tenant     = $this->getTenantProfile();
        $complaints = Complaint::where('tenant_id', $tenant->id)
            ->with(['replies' => fn($q) => $q->latest()->take(1)])
            ->latest()
            ->paginate(15);

        return view('tenant.complaints.index', compact('complaints', 'tenant'));
    }

    public function create(): View
    {
        $tenant = $this->getTenantProfile();
        return view('tenant.complaints.create', compact('tenant'));
    }

    public function store(Request $request): RedirectResponse
    {
        $tenant = $this->getTenantProfile();

        $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'category'    => ['required', 'in:facility,security,cleanliness,noise,other'],
            'priority'    => ['required', 'in:low,medium,high'],
            'photos'      => ['nullable', 'array'],
            'photos.*'    => ['image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ], [
            'title.required'       => 'Judul keluhan wajib diisi.',
            'description.required' => 'Deskripsi keluhan wajib diisi.',
            'category.required'    => 'Kategori wajib dipilih.',
            'priority.required'    => 'Prioritas wajib dipilih.',
        ]);

        $data = $request->except('photos');
        $data['tenant_id'] = $tenant->id;
        $data['status']    = 'new';

        if ($request->hasFile('photos')) {
            $paths = [];
            foreach ($request->file('photos') as $p) {
                $paths[] = $p->store('complaints', 'public');
            }
            $data['photos'] = $paths;
        }

        $complaint = Complaint::create($data);
        NotificationService::complaintCreated($complaint);

        return redirect()->route('tenant.complaints.show', $complaint)
            ->with('success', 'Keluhan berhasil dikirim. Admin akan segera menanganinya.');
    }

    public function show(Complaint $complaint): View
    {
        $this->authorizeComplaint($complaint);
        $complaint->load(['replies.user', 'handler']);
        return view('tenant.complaints.show', compact('complaint'));
    }

    /** Tenant bisa balas/tambah info ke complaint */
    public function reply(Request $request, Complaint $complaint): RedirectResponse
    {
        $this->authorizeComplaint($complaint);

        $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        ComplaintReply::create([
            'complaint_id' => $complaint->id,
            'user_id'      => auth()->id(),
            'message'      => $request->message,
        ]);

        return back()->with('success', 'Balasan berhasil dikirim.');
    }

    private function authorizeComplaint(Complaint $complaint): void
    {
        $tenant = auth()->user()->tenantProfile;
        if (!$tenant || $complaint->tenant_id !== $tenant->id) {
            abort(403, 'Anda tidak memiliki akses ke keluhan ini.');
        }
    }
}
