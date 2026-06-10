<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan {{ $monthLabel }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1e293b; background: white; }
        .header { background: #2563EB; color: white; padding: 24px 30px; margin-bottom: 24px; }
        .header h1 { font-size: 20px; font-weight: bold; margin-bottom: 4px; }
        .header p { font-size: 11px; opacity: 0.8; }
        .content { padding: 0 30px 30px; }
        .summary-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px; }
        .summary-card { border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px; }
        .summary-card .label { font-size: 10px; color: #64748b; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.05em; }
        .summary-card .value { font-size: 18px; font-weight: bold; color: #1e293b; }
        .summary-card.primary { background: #eff6ff; border-color: #bfdbfe; }
        .summary-card.primary .value { color: #2563EB; }
        h2 { font-size: 13px; font-weight: bold; color: #1e293b; margin-bottom: 12px; padding-bottom: 8px; border-bottom: 2px solid #e2e8f0; }
        table { width: 100%; border-collapse: collapse; font-size: 10px; }
        thead tr { background: #f8fafc; }
        th { text-align: left; padding: 10px 12px; font-size: 9px; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; border-bottom: 1px solid #e2e8f0; }
        td { padding: 10px 12px; border-bottom: 1px solid #f1f5f9; }
        tr:nth-child(even) td { background: #f8fafc; }
        .total-row td { font-weight: bold; background: #eff6ff; border-top: 2px solid #bfdbfe; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 9999px; font-size: 9px; font-weight: bold; background: #dcfce7; color: #166534; }
        .footer { margin-top: 30px; padding-top: 16px; border-top: 1px solid #e2e8f0; font-size: 10px; color: #94a3b8; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Keuangan MyKostApp</h1>
        <p>Periode: {{ $monthLabel }} &nbsp;|&nbsp; Dicetak: {{ now()->format('d M Y H:i') }}</p>
    </div>

    <div class="content">
        <div class="summary-grid">
            <div class="summary-card primary">
                <div class="label">Total Pendapatan</div>
                <div class="value">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
            </div>
            <div class="summary-card">
                <div class="label">Jumlah Transaksi</div>
                <div class="value">{{ $payments->count() }}</div>
            </div>
            <div class="summary-card">
                <div class="label">Rata-rata / Transaksi</div>
                <div class="value">Rp {{ $payments->count() > 0 ? number_format($totalRevenue / $payments->count(), 0, ',', '.') : 0 }}</div>
            </div>
        </div>

        <h2>Daftar Pembayaran Terverifikasi</h2>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nomor Invoice</th>
                    <th>Penghuni</th>
                    <th>Kamar</th>
                    <th>Nominal</th>
                    <th>Tanggal Bayar</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $i => $payment)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td style="font-family: monospace; font-size: 9px;">{{ $payment->invoice->invoice_number }}</td>
                    <td>{{ $payment->invoice->tenant->name }}</td>
                    <td>{{ $payment->invoice->tenant->room->number }}</td>
                    <td style="font-weight: 600;">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                    <td>{{ $payment->verified_at?->format('d M Y') }}</td>
                    <td><span class="badge">Lunas</span></td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="4" style="text-align: right;">Total Pendapatan</td>
                    <td>Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                    <td colspan="2"></td>
                </tr>
            </tbody>
        </table>

        <div class="footer">
            Dokumen ini dibuat otomatis oleh MyKostApp &nbsp;•&nbsp; {{ now()->format('d M Y H:i:s') }}
        </div>
    </div>
</body>
</html>
