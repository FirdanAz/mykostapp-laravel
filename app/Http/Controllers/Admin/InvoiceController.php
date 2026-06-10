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

    public function index(Request $request): View
    {
        $query = Invoice::with(['tenant.room'])->latest();

        if ($request->search) {
            $query->where('invoice_number','like','%'.$request->search.'%')
                  ->orWhereHas('tenant', fn($q) => $q->where('name','like','%'.$request->search.'%'));
        }
        if ($request->status) $query->where('status', $request->status);
        if ($request->month)  $query->whereMonth('period_start', $request->month);
        if ($request->year)   $query->whereYear('period_start',  $request->year);

        $invoices = $query->paginate(15)->withQueryString();

        // Summary stats
        $stats = [
            'total'   => Invoice::count(),
            'unpaid'  => Invoice::whereIn('status',['unpaid','overdue'])->count(),
            'pending' => Invoice::where('status','pending_verification')->count(),
            'paid'    => Invoice::where('status','paid')->count(),
        ];

        return view('invoices.index', compact('invoices','stats'));
    }

    public function show(Invoice $invoice): View
    {
        $invoice->load(['tenant.room','payments.verifiedBy']);
        return view('invoices.show', compact('invoice'));
    }

    public function create(): View
    {
        $tenants = Tenant::where('status','active')->with('room')->orderBy('name')->get();
        return view('invoices.create', compact('tenants'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'tenant_id'    => ['required','exists:tenants,id'],
            'amount'       => ['required','numeric','min:1'],
            'due_date'     => ['required','date'],
            'period_start' => ['required','date'],
            'period_end'   => ['required','date','after_or_equal:period_start'],
            'notes'        => ['nullable','string','max:500'],
        ]);

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

        return redirect()->route('invoices.show', $invoice)
            ->with('success', "Invoice {$invoice->invoice_number} berhasil dibuat.");
    }

    public function generateBulk(Request $request): RedirectResponse
    {
        $request->validate([
            'month' => ['required','integer','min:1','max:12'],
            'year'  => ['required','integer','min:2020'],
        ]);

        $created = $this->invoiceService->generateMonthly($request->year, $request->month);

        return redirect()->route('invoices.index')
            ->with('success', "{$created->count()} tagihan berhasil dibuat untuk ".
                Carbon::create($request->year, $request->month)->translatedFormat('F Y').".");
    }

    public function destroy(Invoice $invoice): RedirectResponse
    {
        if ($invoice->status === 'paid') {
            return back()->with('error', 'Invoice yang sudah lunas tidak dapat dihapus.');
        }
        $invoice->delete();
        return redirect()->route('invoices.index')->with('success', 'Invoice berhasil dihapus.');
    }
}
