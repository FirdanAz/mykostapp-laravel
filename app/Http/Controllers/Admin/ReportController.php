<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Room;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $year  = $request->get('year',  now()->year);
        $month = $request->get('month', now()->month);

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate   = $startDate->copy()->endOfMonth();

        // Revenue this month
        $revenue = Payment::where('status','verified')
            ->whereBetween('verified_at', [$startDate, $endDate])
            ->sum('amount');

        // Revenue by month (full year)
        $monthlyData = [];
        for ($m = 1; $m <= 12; $m++) {
            $start = Carbon::create($year, $m, 1)->startOfMonth();
            $end   = $start->copy()->endOfMonth();
            $monthlyData[] = [
                'month'   => $start->translatedFormat('M'),
                'revenue' => (float) Payment::where('status','verified')
                    ->whereBetween('verified_at',[$start,$end])->sum('amount'),
                'invoices'=> Invoice::whereYear('period_start', $year)
                    ->whereMonth('period_start', $m)->count(),
                'paid'    => Invoice::whereYear('period_start', $year)
                    ->whereMonth('period_start', $m)->where('status','paid')->count(),
            ];
        }

        // Occupancy
        $totalRooms    = Room::count();
        $occupiedRooms = Room::where('status','occupied')->count();
        $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 1) : 0;

        // Invoice summary
        $invoiceSummary = [
            'total'   => Invoice::whereYear('period_start', $year)->whereMonth('period_start', $month)->count(),
            'paid'    => Invoice::whereYear('period_start', $year)->whereMonth('period_start', $month)->where('status','paid')->count(),
            'unpaid'  => Invoice::whereYear('period_start', $year)->whereMonth('period_start', $month)->whereIn('status',['unpaid','overdue'])->count(),
            'pending' => Invoice::whereYear('period_start', $year)->whereMonth('period_start', $month)->where('status','pending_verification')->count(),
        ];

        // Top tenants by payment
        $topTenants = Tenant::withSum(['invoices as paid_amount' => function($q) use ($year,$month) {
            $q->where('status','paid')
              ->whereYear('period_start', $year)
              ->whereMonth('period_start', $month);
        }],'amount')->orderByDesc('paid_amount')->take(5)->get();

        return view('reports.index', compact(
            'year','month','revenue','monthlyData',
            'totalRooms','occupiedRooms','occupancyRate',
            'invoiceSummary','topTenants'
        ));
    }

    public function exportPdf(Request $request)
    {
        $year  = $request->get('year',  now()->year);
        $month = $request->get('month', now()->month);

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate   = $startDate->copy()->endOfMonth();

        $payments = Payment::with('invoice.tenant.room')
            ->where('status','verified')
            ->whereBetween('verified_at', [$startDate, $endDate])
            ->get();

        $totalRevenue = $payments->sum('amount');
        $monthLabel   = $startDate->translatedFormat('F Y');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf', compact(
            'payments','totalRevenue','monthLabel','year','month'
        ));

        return $pdf->download("laporan-{$year}-{$month}.pdf");
    }

    public function exportExcel(Request $request)
    {
        $year  = $request->get('year',  now()->year);
        $month = $request->get('month', now()->month);
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\ReportExport($year, $month),
            "laporan-{$year}-{$month}.xlsx"
        );
    }
}
