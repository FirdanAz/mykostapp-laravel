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
    public function index(Request $request): View
    {
        $query = Payment::with(['invoice.tenant.room'])->latest();

        if ($request->status)  $query->where('status', $request->status);
        if ($request->search) {
            $query->whereHas('invoice', fn($q) =>
                $q->where('invoice_number','like','%'.$request->search.'%')
                  ->orWhereHas('tenant', fn($tq) => $tq->where('name','like','%'.$request->search.'%'))
            );
        }

        $payments = $query->paginate(15)->withQueryString();
        return view('payments.index', compact('payments'));
    }

    public function show(Payment $payment): View
    {
        $payment->load(['invoice.tenant.room','verifiedBy']);
        return view('payments.show', compact('payment'));
    }

    public function upload(Invoice $invoice): View
    {
        if ($invoice->status === 'paid') abort(403, 'Invoice sudah lunas.');
        return view('payments.upload', compact('invoice'));
    }

    public function store(PaymentUploadRequest $request, Invoice $invoice): RedirectResponse
    {
        $proofPath = $request->file('proof_file')->store('payments','public');

        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'proof_file' => $proofPath,
            'amount'     => $request->amount,
            'status'     => 'pending',
        ]);

        $invoice->update(['status' => 'pending_verification']);

        NotificationService::paymentSubmitted($payment);

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Bukti pembayaran berhasil diunggah. Menunggu verifikasi.');
    }

    public function verify(Payment $payment): RedirectResponse
    {
        $payment->update([
            'status'      => 'verified',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);

        $payment->invoice->update(['status' => 'paid']);

        NotificationService::paymentVerified($payment);

        return back()->with('success', 'Pembayaran berhasil diverifikasi.');
    }

    public function reject(Request $request, Payment $payment): RedirectResponse
    {
        $request->validate([
            'rejection_reason' => ['required','string','max:500'],
        ],['rejection_reason.required' => 'Alasan penolakan wajib diisi.']);

        $payment->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'verified_by'      => auth()->id(),
            'verified_at'      => now(),
        ]);

        $payment->invoice->update(['status' => 'rejected']);

        NotificationService::paymentRejected($payment);

        return back()->with('success', 'Pembayaran telah ditolak.');
    }
}
