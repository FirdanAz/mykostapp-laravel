<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PaymentUploadRequest;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PaymentController extends Controller
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
        $query = Payment::with(['invoice.tenant.room'])
            ->whereHas('invoice.tenant.room', fn($q) => $q->where('kost_id', $kost->id))
            ->latest();

        if ($request->status) $query->where('status', $request->status);
        if ($request->search) {
            $query->whereHas('invoice', fn($q) =>
                $q->where('invoice_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('tenant', fn($tq) => $tq->where('name', 'like', '%' . $request->search . '%'))
            );
        }

        $payments = $query->paginate(15)->withQueryString();
        return view('payments.index', compact('payments', 'kost'));
    }

    public function show(Payment $payment): View
    {
        $this->authorizePayment($payment);
        $payment->load(['invoice.tenant.room', 'verifiedBy']);
        return view('payments.show', compact('payment'));
    }

    /** Verifikasi pembayaran oleh admin */
    public function verify(Payment $payment): RedirectResponse
    {
        $this->authorizePayment($payment);

        $payment->update([
            'status'      => 'verified',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);

        $payment->invoice->update(['status' => 'paid']);

        NotificationService::paymentVerified($payment);

        return back()->with('success', 'Pembayaran berhasil diverifikasi.');
    }

    /** Tolak pembayaran oleh admin */
    public function reject(Request $request, Payment $payment): RedirectResponse
    {
        $this->authorizePayment($payment);

        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ], ['rejection_reason.required' => 'Alasan penolakan wajib diisi.']);

        $payment->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'verified_by'      => auth()->id(),
            'verified_at'      => now(),
        ]);

        $payment->invoice->update(['status' => 'unpaid']);

        NotificationService::paymentRejected($payment);

        return back()->with('success', 'Pembayaran telah ditolak.');
    }

    private function authorizePayment(Payment $payment): void
    {
        $kost = auth()->user()->kost;
        if (!$kost || $payment->invoice?->tenant?->room?->kost_id !== $kost->id) {
            abort(403, 'Anda tidak memiliki akses ke pembayaran ini.');
        }
    }
}
