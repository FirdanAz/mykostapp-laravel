<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Room;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $kost = $user->kost;

        // Jika admin belum punya kos, tampilkan dashboard kosong
        if (!$kost) {
            return view('dashboard.index', [
                'kost'             => null,
                'totalRooms'       => 0,
                'occupiedRooms'    => 0,
                'availableRooms'   => 0,
                'maintenanceRooms' => 0,
                'monthlyRevenue'   => 0,
                'unpaidInvoices'   => 0,
                'activeComplaints' => 0,
                'activeTenants'    => 0,
                'revenueChart'     => [],
                'occupancyChart'   => [],
                'recentInvoices'   => collect(),
                'recentPayments'   => collect(),
                'recentComplaints' => collect(),
            ]);
        }

        // Scope semua query ke kos milik admin ini
        $kostId = $kost->id;

        $totalRooms       = Room::where('kost_id', $kostId)->count();
        $occupiedRooms    = Room::where('kost_id', $kostId)->where('status', 'occupied')->count();
        $availableRooms   = Room::where('kost_id', $kostId)->where('status', 'available')->count();
        $maintenanceRooms = Room::where('kost_id', $kostId)->where('status', 'maintenance')->count();

        $currentMonth = Carbon::now()->month;
        $currentYear  = Carbon::now()->year;

        // Revenue bulan ini (dari kos ini)
        $monthlyRevenue = Payment::whereHas('invoice.tenant.room', fn($q) => $q->where('kost_id', $kostId))
            ->where('status', 'verified')
            ->whereMonth('verified_at', $currentMonth)
            ->whereYear('verified_at', $currentYear)
            ->sum('amount');

        // Tagihan belum bayar dari kos ini
        $unpaidInvoices = Invoice::whereHas('tenant.room', fn($q) => $q->where('kost_id', $kostId))
            ->whereIn('status', ['unpaid', 'overdue'])
            ->count();

        // Keluhan aktif dari kos ini
        $activeComplaints = Complaint::whereHas('tenant.room', fn($q) => $q->where('kost_id', $kostId))
            ->whereIn('status', ['new', 'in_progress'])
            ->count();

        // Tenant aktif dari kos ini
        $activeTenants = Tenant::whereHas('room', fn($q) => $q->where('kost_id', $kostId))
            ->where('status', 'active')
            ->count();

        // Chart: revenue 6 bulan terakhir
        $revenueChart = [];
        for ($i = 5; $i >= 0; $i--) {
            $date  = Carbon::now()->subMonths($i);
            $total = Payment::whereHas('invoice.tenant.room', fn($q) => $q->where('kost_id', $kostId))
                ->where('status', 'verified')
                ->whereMonth('verified_at', $date->month)
                ->whereYear('verified_at', $date->year)
                ->sum('amount');
            $revenueChart[] = [
                'month' => $date->translatedFormat('M Y'),
                'total' => (float) $total,
            ];
        }

        // Chart: tingkat hunian 6 bulan terakhir
        $occupancyChart = [];
        for ($i = 5; $i >= 0; $i--) {
            $date     = Carbon::now()->subMonths($i);
            $occupied = Tenant::whereHas('room', fn($q) => $q->where('kost_id', $kostId))
                ->where('status', 'active')
                ->whereDate('start_date', '<=', $date->endOfMonth())
                ->count();
            $occupancyChart[] = [
                'month'    => $date->translatedFormat('M Y'),
                'occupied' => $occupied,
                'total'    => $totalRooms,
                'rate'     => $totalRooms > 0 ? round(($occupied / $totalRooms) * 100) : 0,
            ];
        }

        // Aktivitas terbaru (scope ke kos ini)
        $recentInvoices = Invoice::with('tenant.room')
            ->whereHas('tenant.room', fn($q) => $q->where('kost_id', $kostId))
            ->latest()->take(5)->get();

        $recentPayments = Payment::with('invoice.tenant')
            ->whereHas('invoice.tenant.room', fn($q) => $q->where('kost_id', $kostId))
            ->latest()->take(5)->get();

        $recentComplaints = Complaint::with('tenant')
            ->whereHas('tenant.room', fn($q) => $q->where('kost_id', $kostId))
            ->latest()->take(5)->get();

        return view('dashboard.index', compact(
            'kost',
            'totalRooms', 'occupiedRooms', 'availableRooms', 'maintenanceRooms',
            'monthlyRevenue', 'unpaidInvoices', 'activeComplaints', 'activeTenants',
            'revenueChart', 'occupancyChart',
            'recentInvoices', 'recentPayments', 'recentComplaints'
        ));
    }
}
