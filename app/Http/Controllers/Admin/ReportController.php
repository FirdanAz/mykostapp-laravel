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
    private function getKost()
    {
        $kost = auth()->user()->kost;
        if (!$kost) abort(403, 'Setup kos Anda terlebih dahulu.');
        return $kost;
    }

    public function index(Request $request): View
    {
        $kost  = $this->getKost();
        $kostId = $kost->id;

        $year  = $request->get('year',  now()->year);
        $month = $request->get('month', now()->month);

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate   = $startDate->copy()->endOfMonth();

        $revenue = Payment::whereHas('invoice.tenant.room', fn($q) => $q->where('kost_id', $kostId))
            ->where('status', 'verified')
            ->whereBetween('verified_at', [$startDate, $endDate])
            ->sum('amount');

        $monthlyData = [];
        for ($m = 1; $m <= 12; $m++) {
            $start = Carbon::create($year, $m, 1)->startOfMonth();
            $end   = $start->copy()->endOfMonth();
            $monthlyData[] = [
                'month'    => $start->translatedFormat('M'),
                'revenue'  => (float) Payment::whereHas('invoice.tenant.room', fn($q) => $q->where('kost_id', $kostId))
                    ->where('status', 'verified')->whereBetween('verified_at', [$start, $end])->sum('amount'),
                'invoices' => Invoice::whereHas('tenant.room', fn($q) => $q->where('kost_id', $kostId))
                    ->whereYear('period_start', $year)->whereMonth('period_start', $m)->count(),
                'paid'     => Invoice::whereHas('tenant.room', fn($q) => $q->where('kost_id', $kostId))
                    ->whereYear('period_start', $year)->whereMonth('period_start', $m)->where('status', 'paid')->count(),
            ];
        }

        $totalRooms    = Room::where('kost_id', $kostId)->count();
        $occupiedRooms = Room::where('kost_id', $kostId)->where('status', 'occupied')->count();
        $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 1) : 0;

        $baseInvoice = Invoice::whereHas('tenant.room', fn($q) => $q->where('kost_id', $kostId))
            ->whereYear('period_start', $year)->whereMonth('period_start', $month);

        $invoiceSummary = [
            'total'   => (clone $baseInvoice)->count(),
            'paid'    => (clone $baseInvoice)->where('status', 'paid')->count(),
            'unpaid'  => (clone $baseInvoice)->whereIn('status', ['unpaid', 'overdue'])->count(),
            'pending' => (clone $baseInvoice)->where('status', 'pending_verification')->count(),
        ];

        $topTenants = Tenant::whereHas('room', fn($q) => $q->where('kost_id', $kostId))
            ->withSum(['invoices as paid_amount' => function ($q) use ($year, $month) {
                $q->where('status', 'paid')
                  ->whereYear('period_start', $year)
                  ->whereMonth('period_start', $month);
            }], 'amount')
            ->orderByDesc('paid_amount')
            ->take(5)
            ->get();

        return view('reports.index', compact(
            'kost', 'year', 'month', 'revenue', 'monthlyData',
            'totalRooms', 'occupiedRooms', 'occupancyRate',
            'invoiceSummary', 'topTenants'
        ));
    }

    public function exportPdf(Request $request)
    {
        $kost   = $this->getKost();
        $kostId = $kost->id;

        $year  = $request->get('year',  now()->year);
        $month = $request->get('month', now()->month);

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate   = $startDate->copy()->endOfMonth();

        $payments = Payment::with('invoice.tenant.room')
            ->whereHas('invoice.tenant.room', fn($q) => $q->where('kost_id', $kostId))
            ->where('status', 'verified')
            ->whereBetween('verified_at', [$startDate, $endDate])
            ->get();

        $totalRevenue = $payments->sum('amount');
        $monthLabel   = $startDate->translatedFormat('F Y');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf', compact(
            'payments', 'totalRevenue', 'monthLabel', 'year', 'month', 'kost'
        ));

        return $pdf->download("laporan-{$kost->name}-{$year}-{$month}.pdf");
    }

    public function exportExcel(Request $request)
    {
        $kost  = $this->getKost();
        $year  = $request->get('year',  now()->year);
        $month = $request->get('month', now()->month);
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\ReportExport($year, $month, $kost->id),
            "laporan-{$kost->name}-{$year}-{$month}.xlsx"
        );
    }
}
