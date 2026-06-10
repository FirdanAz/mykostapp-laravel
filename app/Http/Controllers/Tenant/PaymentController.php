<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    private function getTenantProfile()
    {
        $profile = auth()->user()->tenantProfile;
        if (!$profile) abort(403, 'Anda belum terdaftar sebagai penghuni kos.');
        return $profile;
    }

    /** Form upload bukti pembayaran */
    public function upload(Invoice $invoice): View
    {
        $tenant = $this->getTenantProfile();

        if ($invoice->tenant_id !== $tenant->id) {
            abort(403, 'Anda tidak memiliki akses ke tagihan ini.');
        }

        if ($invoice->status === 'paid') {
            return redirect()->route('tenant.invoices.show', $invoice)
                ->with('info', 'Tagihan ini sudah lunas.');
        }

        if ($invoice->status === 'pending_verification') {
            return redirect()->route('tenant.invoices.show', $invoice)
                ->with('info', 'Bukti pembayaran sudah diunggah dan sedang diverifikasi.');
        }

        return view('tenant.payments.upload', compact('invoice'));
    }

    /** Simpan bukti pembayaran */
    public function store(Request $request, Invoice $invoice): RedirectResponse
    {
        $tenant = $this->getTenantProfile();

        if ($invoice->tenant_id !== $tenant->id) {
            abort(403, 'Anda tidak memiliki akses ke tagihan ini.');
        }

        if (in_array($invoice->status, ['paid', 'pending_verification'])) {
            return back()->with('error', 'Tagihan ini tidak bisa dibayar saat ini.');
        }

        $request->validate([
            'proof_file' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'amount'     => ['required', 'numeric', 'min:1'],
            'notes'      => ['nullable', 'string', 'max:500'],
        ], [
            'proof_file.required' => 'Bukti pembayaran wajib diunggah.',
            'proof_file.mimes'    => 'Format file: JPG, PNG, atau PDF.',
            'proof_file.max'      => 'Ukuran file maksimal 5 MB.',
            'amount.required'     => 'Jumlah pembayaran wajib diisi.',
        ]);

        $proofPath = $request->file('proof_file')->store('payments', 'public');

        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'proof_file' => $proofPath,
            'amount'     => $request->amount,
            'notes'      => $request->notes,
            'status'     => 'pending',
        ]);

        $invoice->update(['status' => 'pending_verification']);

        NotificationService::paymentSubmitted($payment);

        return redirect()->route('tenant.invoices.show', $invoice)
            ->with('success', 'Bukti pembayaran berhasil diunggah. Menunggu verifikasi admin.');
    }
}
