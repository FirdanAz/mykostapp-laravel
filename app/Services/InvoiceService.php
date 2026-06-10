<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class InvoiceService
{
    public function generateMonthly(int $year, int $month): Collection
    {
        $created  = collect();
        $dueDays  = (int) \App\Models\Setting::get('invoice_due_days', 10);
        $tenants  = Tenant::where('status','active')->with('room')->get();

        foreach ($tenants as $tenant) {
            $periodStart = Carbon::create($year, $month, 1)->startOfMonth();
            $periodEnd   = $periodStart->copy()->endOfMonth();

            $exists = Invoice::where('tenant_id', $tenant->id)
                ->where('period_start', $periodStart->toDateString())
                ->exists();

            if (!$exists) {
                $invoice = Invoice::create([
                    'tenant_id'      => $tenant->id,
                    'invoice_number' => Invoice::generateNumber(),
                    'amount'         => $tenant->room->price,
                    'due_date'       => $periodStart->copy()->addDays($dueDays),
                    'period_start'   => $periodStart,
                    'period_end'     => $periodEnd,
                    'status'         => 'unpaid',
                ]);

                NotificationService::invoiceCreated($invoice);
                $created->push($invoice);
            }
        }

        return $created;
    }

    public function markOverdue(): int
    {
        return Invoice::where('status','unpaid')
            ->where('due_date','<', now()->toDateString())
            ->update(['status' => 'overdue']);
    }
}
