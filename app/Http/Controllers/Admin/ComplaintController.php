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
    public function index(Request $request): View
    {
        $query = Complaint::with(['tenant.room','handler'])->latest();

        if ($request->search) {
            $query->where('title','like','%'.$request->search.'%')
                  ->orWhereHas('tenant', fn($q) => $q->where('name','like','%'.$request->search.'%'));
        }
        if ($request->status)   $query->where('status',   $request->status);
        if ($request->priority) $query->where('priority', $request->priority);
        if ($request->category) $query->where('category', $request->category);

        $complaints = $query->paginate(15)->withQueryString();
        return view('complaints.index', compact('complaints'));
    }

    public function show(Complaint $complaint): View
    {
        $complaint->load(['tenant.room','handler','replies.user']);
        return view('complaints.show', compact('complaint'));
    }

    public function create(): View
    {
        $tenants = Tenant::where('status','active')->orderBy('name')->get();
        return view('complaints.create', compact('tenants'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'tenant_id'   => ['required','exists:tenants,id'],
            'title'       => ['required','string','max:255'],
            'description' => ['required','string'],
            'category'    => ['required','in:facility,security,cleanliness,noise,other'],
            'priority'    => ['required','in:low,medium,high'],
            'photos'      => ['nullable','array'],
            'photos.*'    => ['image','mimes:jpg,jpeg,png','max:2048'],
        ]);

        $data = $request->except('photos');

        if ($request->hasFile('photos')) {
            $paths = [];
            foreach ($request->file('photos') as $p) {
                $paths[] = $p->store('complaints','public');
            }
            $data['photos'] = $paths;
        }

        $complaint = Complaint::create($data);
        NotificationService::complaintCreated($complaint);

        return redirect()->route('complaints.show', $complaint)
            ->with('success', 'Keluhan berhasil dicatat.');
    }

    public function reply(Request $request, Complaint $complaint): RedirectResponse
    {
        $request->validate([
            'message' => ['required','string','max:2000'],
        ],['message.required' => 'Pesan balasan wajib diisi.']);

        ComplaintReply::create([
            'complaint_id' => $complaint->id,
            'user_id'      => auth()->id(),
            'message'      => $request->message,
        ]);

        return back()->with('success', 'Balasan berhasil dikirim.');
    }

    public function updateStatus(Request $request, Complaint $complaint): RedirectResponse
    {
        $request->validate([
            'status' => ['required','in:new,in_progress,resolved,rejected'],
        ]);

        $data = ['status' => $request->status, 'handled_by' => auth()->id()];

        if ($request->status === 'resolved') {
            $data['resolved_at'] = now();
        }

        $complaint->update($data);

        return back()->with('success', 'Status keluhan berhasil diperbarui.');
    }
}
