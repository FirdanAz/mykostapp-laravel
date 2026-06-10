<?php

namespace App\Exports;

use App\Models\Payment;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles
{
    public function __construct(private int $year, private int $month) {}

    public function collection()
    {
        $start = Carbon::create($this->year, $this->month, 1)->startOfMonth();
        $end   = $start->copy()->endOfMonth();

        return Payment::with('invoice.tenant.room')
            ->where('status','verified')
            ->whereBetween('verified_at', [$start, $end])
            ->get();
    }

    public function headings(): array
    {
        return ['No','Invoice','Penghuni','Kamar','Nominal','Tanggal Bayar','Diverifikasi Oleh'];
    }

    public function map($payment): array
    {
        static $no = 0;
        $no++;
        return [
            $no,
            $payment->invoice->invoice_number,
            $payment->invoice->tenant->name,
            $payment->invoice->tenant->room->number,
            $payment->amount,
            $payment->verified_at?->format('d/m/Y H:i'),
            $payment->verifiedBy?->name ?? '-',
        ];
    }

    public function title(): string
    {
        return 'Laporan '.Carbon::create($this->year, $this->month)->translatedFormat('F Y');
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'color' => ['rgb' => '2563EB']], 'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true]],
        ];
    }
}
