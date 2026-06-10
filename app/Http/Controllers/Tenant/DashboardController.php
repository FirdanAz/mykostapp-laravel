<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user          = auth()->user();
        $tenantProfile = $user->tenantProfile;

        if (!$tenantProfile) {
            $rentalApplications = $user->rentalApplications()->with('room.kost')->latest()->take(3)->get();
            return view('tenant.dashboard', [
                'tenantProfile'      => null,
                'room'               => null,
                'unpaidInvoices'     => collect(),
                'recentPayments'     => collect(),
                'openComplaints'     => 0,
                'rentalApplications' => $rentalApplications,
            ]);
        }

        $rentalApplications = $user->rentalApplications()->with('room.kost')->latest()->take(3)->get();

        $tenantId = $tenantProfile->id;

        $unpaidInvoices = Invoice::where('tenant_id', $tenantId)
            ->whereIn('status', ['unpaid', 'overdue', 'pending_verification'])
            ->with('payments')
            ->latest()
            ->get();

        $recentPayments = Payment::whereHas('invoice', fn($q) => $q->where('tenant_id', $tenantId))
            ->with('invoice')
            ->latest()
            ->take(5)
            ->get();

        $openComplaints = Complaint::where('tenant_id', $tenantId)
            ->whereIn('status', ['new', 'in_progress'])
            ->count();

        $room = $tenantProfile->room;

        return view('tenant.dashboard', compact(
            'tenantProfile', 'room', 'unpaidInvoices', 'recentPayments', 'openComplaints', 'rentalApplications'
        ));
    }
}
