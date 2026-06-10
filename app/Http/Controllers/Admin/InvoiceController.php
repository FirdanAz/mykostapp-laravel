<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Tenant;
use App\Services\InvoiceService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function __construct(private InvoiceService $invoiceService) {}

    private function getKost()
    {
        $kost = auth()->user()->kost;
        if (!$kost) abort(403, 'Setup kos Anda terlebih dahulu.');
        return $kost;
    }

    public function index(Request $request): View
    {
        $kost  = $this->getKost();
        $query = Invoice::with(['tenant.room'])
            ->whereHas('tenant.room', fn($q) => $q->where('kost_id', $kost->id))
            ->latest();

        if ($request->search) {
            $query->where('invoice_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('tenant', fn($q) => $q->where('name', 'like', '%' . $request->search . '%'));
        }
        if ($request->status) $query->where('status', $request->status);
        if ($request->month)  $query->whereMonth('period_start', $request->month);
        if ($request->year)   $query->whereYear('period_start', $request->year);

        $invoices = $query->paginate(15)->withQueryString();

        $baseQuery = Invoice::whereHas('tenant.room', fn($q) => $q->where('kost_id', $kost->id));
        $stats = [
            'total'   => (clone $baseQuery)->count(),
            'unpaid'  => (clone $baseQuery)->whereIn('status', ['unpaid', 'overdue'])->count(),
            'pending' => (clone $baseQuery)->where('status', 'pending_verification')->count(),
            'paid'    => (clone $baseQuery)->where('status', 'paid')->count(),
        ];

        return view('invoices.index', compact('invoices', 'stats', 'kost'));
    }

    public function show(Invoice $invoice): View
    {
        $this->authorizeInvoice($invoice);
        $invoice->load(['tenant.room', 'payments.verifiedBy']);
        return view('invoices.show', compact('invoice'));
    }

    public function create(): View
    {
        $kost    = $this->getKost();
        $tenants = Tenant::whereHas('room', fn($q) => $q->where('kost_id', $kost->id))
            ->where('status', 'active')
            ->with('room')
            ->orderBy('name')
            ->get();
        return view('invoices.create', compact('tenants', 'kost'));
    }

    public function store(Request $request): RedirectResponse
    {
        $kost = $this->getKost();

        $request->validate([
            'tenant_id'    => ['required', 'exists:tenants,id'],
            'amount'       => ['required', 'numeric', 'min:1'],
            'due_date'     => ['required', 'date'],
            'period_start' => ['required', 'date'],
            'period_end'   => ['required', 'date', 'after_or_equal:period_start'],
            'notes'        => ['nullable', 'string', 'max:500'],
        ]);

        // Pastikan tenant milik kos ini
        $tenant = Tenant::whereHas('room', fn($q) => $q->where('kost_id', $kost->id))
            ->findOrFail($request->tenant_id);

        $invoice = Invoice::create([
            'tenant_id'      => $request->tenant_id,
            'invoice_number' => Invoice::generateNumber(),
            'amount'         => $request->amount,
            'due_date'       => $request->due_date,
            'period_start'   => $request->period_start,
            'period_end'     => $request->period_end,
            'notes'          => $request->notes,
            'status'         => 'unpaid',
        ]);

        \App\Services\NotificationService::invoiceCreated($invoice);

        return redirect()->route('admin.invoices.show', $invoice)
            ->with('success', "Invoice {$invoice->invoice_number} berhasil dibuat.");
    }

    public function generateBulk(Request $request): RedirectResponse
    {
        $kost = $this->getKost();

        $request->validate([
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'year'  => ['required', 'integer', 'min:2020'],
        ]);

        $created = $this->invoiceService->generateMonthly($request->year, $request->month, $kost->id);

        return redirect()->route('admin.invoices.index')
            ->with('success', "{$created->count()} tagihan berhasil dibuat untuk " .
                Carbon::create($request->year, $request->month)->translatedFormat('F Y') . ".");
    }

    public function destroy(Invoice $invoice): RedirectResponse
    {
        $this->authorizeInvoice($invoice);

        if ($invoice->status === 'paid') {
            return back()->with('error', 'Invoice yang sudah lunas tidak dapat dihapus.');
        }
        $invoice->delete();
        return redirect()->route('admin.invoices.index')->with('success', 'Invoice berhasil dihapus.');
    }

    private function authorizeInvoice(Invoice $invoice): void
    {
        $kost = auth()->user()->kost;
        if (!$kost || $invoice->tenant?->room?->kost_id !== $kost->id) {
            abort(403, 'Anda tidak memiliki akses ke invoice ini.');
        }
    }
}
