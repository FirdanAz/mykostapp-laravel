<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    private function getTenantProfile()
    {
        $profile = auth()->user()->tenantProfile;
        if (!$profile) abort(403, 'Anda belum terdaftar sebagai penghuni kos.');
        return $profile;
    }

    public function index(): View
    {
        $tenant   = $this->getTenantProfile();
        $invoices = Invoice::where('tenant_id', $tenant->id)
            ->with(['payments'])
            ->latest()
            ->paginate(15);

        return view('tenant.invoices.index', compact('invoices', 'tenant'));
    }

    public function show(Invoice $invoice): View
    {
        $tenant = $this->getTenantProfile();

        if ($invoice->tenant_id !== $tenant->id) {
            abort(403, 'Anda tidak memiliki akses ke tagihan ini.');
        }

        $invoice->load(['payments.verifiedBy', 'tenant.room']);
        return view('tenant.invoices.show', compact('invoice'));
    }
}
