<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\ComplaintReply;
use App\Models\Tenant;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ComplaintController extends Controller
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
        $query = Complaint::with(['tenant.room', 'handler'])
            ->whereHas('tenant.room', fn($q) => $q->where('kost_id', $kost->id))
            ->latest();

        if ($request->search) {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhereHas('tenant', fn($q) => $q->where('name', 'like', '%' . $request->search . '%'));
        }
        if ($request->status)   $query->where('status', $request->status);
        if ($request->priority) $query->where('priority', $request->priority);
        if ($request->category) $query->where('category', $request->category);

        $complaints = $query->paginate(15)->withQueryString();
        return view('complaints.index', compact('complaints', 'kost'));
    }

    public function show(Complaint $complaint): View
    {
        $this->authorizeComplaint($complaint);
        $complaint->load(['tenant.room', 'handler', 'replies.user']);
        return view('complaints.show', compact('complaint'));
    }

    public function create(): View
    {
        $kost    = $this->getKost();
        $tenants = Tenant::whereHas('room', fn($q) => $q->where('kost_id', $kost->id))
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
        return view('complaints.create', compact('tenants', 'kost'));
    }

    public function store(Request $request): RedirectResponse
    {
        $kost = $this->getKost();

        $request->validate([
            'tenant_id'   => ['required', 'exists:tenants,id'],
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'category'    => ['required', 'in:facility,security,cleanliness,noise,other'],
            'priority'    => ['required', 'in:low,medium,high'],
            'photos'      => ['nullable', 'array'],
            'photos.*'    => ['image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        // Pastikan tenant milik kos ini
        Tenant::whereHas('room', fn($q) => $q->where('kost_id', $kost->id))
            ->findOrFail($request->tenant_id);

        $data = $request->except('photos');

        if ($request->hasFile('photos')) {
            $paths = [];
            foreach ($request->file('photos') as $p) {
                $paths[] = $p->store('complaints', 'public');
            }
            $data['photos'] = $paths;
        }

        $complaint = Complaint::create($data);
        NotificationService::complaintCreated($complaint);

        return redirect()->route('admin.complaints.show', $complaint)
            ->with('success', 'Keluhan berhasil dicatat.');
    }

    public function reply(Request $request, Complaint $complaint): RedirectResponse
    {
        $this->authorizeComplaint($complaint);

        $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ], ['message.required' => 'Pesan balasan wajib diisi.']);

        ComplaintReply::create([
            'complaint_id' => $complaint->id,
            'user_id'      => auth()->id(),
            'message'      => $request->message,
        ]);

        return back()->with('success', 'Balasan berhasil dikirim.');
    }

    public function updateStatus(Request $request, Complaint $complaint): RedirectResponse
    {
        $this->authorizeComplaint($complaint);

        $request->validate([
            'status' => ['required', 'in:new,in_progress,resolved,rejected'],
        ]);

        $data = ['status' => $request->status, 'handled_by' => auth()->id()];

        if ($request->status === 'resolved') {
            $data['resolved_at'] = now();
        }

        $complaint->update($data);

        return back()->with('success', 'Status keluhan berhasil diperbarui.');
    }

    private function authorizeComplaint(Complaint $complaint): void
    {
        $kost = auth()->user()->kost;
        if (!$kost || $complaint->tenant?->room?->kost_id !== $kost->id) {
            abort(403, 'Anda tidak memiliki akses ke keluhan ini.');
        }
    }
}
