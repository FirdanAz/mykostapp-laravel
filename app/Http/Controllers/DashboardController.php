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
        $totalRooms    = Room::count();
        $occupiedRooms = Room::where('status','occupied')->count();
        $availableRooms= Room::where('status','available')->count();
        $maintenanceRooms = Room::where('status','maintenance')->count();

        $currentMonth  = Carbon::now()->month;
        $currentYear   = Carbon::now()->year;

        $monthlyRevenue = Payment::where('status','verified')
            ->whereMonth('verified_at', $currentMonth)
            ->whereYear('verified_at', $currentYear)
            ->sum('amount');

        $unpaidInvoices = Invoice::whereIn('status',['unpaid','overdue'])->count();
        $activeComplaints = Complaint::whereIn('status',['new','in_progress'])->count();
        $activeTenants  = Tenant::where('status','active')->count();

        // Chart data: last 6 months revenue
        $revenueChart = [];
        for ($i = 5; $i >= 0; $i--) {
            $date  = Carbon::now()->subMonths($i);
            $total = Payment::where('status','verified')
                ->whereMonth('verified_at', $date->month)
                ->whereYear('verified_at',  $date->year)
                ->sum('amount');
            $revenueChart[] = [
                'month'  => $date->translatedFormat('M Y'),
                'total'  => (float) $total,
            ];
        }

        // Occupancy chart: last 6 months
        $occupancyChart = [];
        for ($i = 5; $i >= 0; $i--) {
            $date     = Carbon::now()->subMonths($i);
            $occupied = Tenant::where('status','active')
                ->whereDate('start_date','<=',$date->endOfMonth())
                ->count();
            $occupancyChart[] = [
                'month'    => $date->translatedFormat('M Y'),
                'occupied' => $occupied,
                'total'    => $totalRooms,
                'rate'     => $totalRooms > 0 ? round(($occupied / $totalRooms) * 100) : 0,
            ];
        }

        // Recent activity
        $recentInvoices  = Invoice::with('tenant.room')->latest()->take(5)->get();
        $recentPayments  = Payment::with('invoice.tenant')->latest()->take(5)->get();
        $recentComplaints= Complaint::with('tenant')->latest()->take(5)->get();

        return view('dashboard.index', compact(
            'totalRooms','occupiedRooms','availableRooms','maintenanceRooms',
            'monthlyRevenue','unpaidInvoices','activeComplaints','activeTenants',
            'revenueChart','occupancyChart',
            'recentInvoices','recentPayments','recentComplaints'
        ));
    }
}
