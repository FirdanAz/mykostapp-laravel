@extends('layouts.app')
@section('title','Laporan Keuangan')
@section('page-title','Laporan Keuangan')
@section('breadcrumb') <span class="mx-1">/</span> Laporan @endsection

@section('content')
<div class="space-y-5">

{{-- Period Selector --}}
<div class="bg-white rounded-2xl border border-slate-200 p-4">
    <form method="GET" class="flex flex-wrap items-center gap-3">
        <select name="month" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
            @for($m=1; $m<=12; $m++)
            <option value="{{ $m }}" {{ $month == $m ? 'selected':'' }}>
                {{ \Carbon\Carbon::create(null,$m)->translatedFormat('F') }}
            </option>
            @endfor
        </select>
        <select name="year" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
            @for($y=date('Y'); $y>=date('Y')-3; $y--)
            <option value="{{ $y }}" {{ $year == $y ? 'selected':'' }}>{{ $y }}</option>
            @endfor
        </select>
        <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition-colors">
            Tampilkan
        </button>
        <div class="ml-auto flex gap-2">
            <a href="{{ route('admin.reports.pdf', ['month'=>$month,'year'=>$year]) }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                PDF
            </a>
            <a href="{{ route('admin.reports.excel', ['month'=>$month,'year'=>$year]) }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Excel
            </a>
        </div>
    </form>
</div>

{{-- Summary Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-white rounded-2xl border border-slate-200 p-5">
        <div class="w-9 h-9 bg-emerald-100 rounded-xl flex items-center justify-center mb-3">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 8v1M12 3v1m0 16v1m9-9h-1M4 12H3"/></svg>
        </div>
        <p class="text-2xl font-bold text-slate-900">Rp {{ number_format($revenue,0,',','.') }}</p>
        <p class="text-sm text-slate-500 mt-0.5">Pendapatan</p>
        <p class="text-xs text-slate-400 mt-0.5">{{ \Carbon\Carbon::create($year,$month)->translatedFormat('F Y') }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-5">
        <div class="w-9 h-9 bg-blue-100 rounded-xl flex items-center justify-center mb-3">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <p class="text-2xl font-bold text-slate-900">{{ $invoiceSummary['paid'] }}<span class="text-slate-400 text-sm font-normal">/{{ $invoiceSummary['total'] }}</span></p>
        <p class="text-sm text-slate-500 mt-0.5">Invoice Lunas</p>
        <p class="text-xs text-slate-400 mt-0.5">{{ $invoiceSummary['unpaid'] }} belum dibayar</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-5">
        <div class="w-9 h-9 bg-indigo-100 rounded-xl flex items-center justify-center mb-3">
            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
        </div>
        <p class="text-2xl font-bold text-slate-900">{{ $occupancyRate }}%</p>
        <p class="text-sm text-slate-500 mt-0.5">Tingkat Hunian</p>
        <p class="text-xs text-slate-400 mt-0.5">{{ $occupiedRooms }}/{{ $totalRooms }} kamar</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-5">
        <div class="w-9 h-9 bg-amber-100 rounded-xl flex items-center justify-center mb-3">
            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <p class="text-2xl font-bold text-slate-900">{{ $invoiceSummary['pending'] }}</p>
        <p class="text-sm text-slate-500 mt-0.5">Menunggu Verifikasi</p>
        <p class="text-xs text-slate-400 mt-0.5">Perlu ditindaklanjuti</p>
    </div>
</div>

{{-- Annual Revenue Chart --}}
<div class="bg-white rounded-2xl border border-slate-200 p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="font-semibold text-slate-800">Pendapatan Per Bulan {{ $year }}</h3>
            <p class="text-xs text-slate-400 mt-0.5">Berdasarkan pembayaran terverifikasi</p>
        </div>
        <div class="text-right">
            <p class="text-xs text-slate-400">Total Tahun {{ $year }}</p>
            <p class="font-bold text-slate-800">Rp {{ number_format(collect($monthlyData)->sum('revenue'),0,',','.') }}</p>
        </div>
    </div>
    <canvas id="annualChart" height="120"></canvas>
</div>

{{-- Invoice Status Chart + Top Tenants --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-2xl border border-slate-200 p-6">
        <h3 class="font-semibold text-slate-800 mb-5">Status Invoice {{ \Carbon\Carbon::create($year,$month)->translatedFormat('F Y') }}</h3>
        <div class="flex items-center justify-center">
            <canvas id="statusChart" width="200" height="200"></canvas>
        </div>
        <div class="mt-4 grid grid-cols-2 gap-2">
            @foreach(['Lunas'=>['val'=>$invoiceSummary['paid'],'color'=>'bg-green-500'],'Belum Bayar'=>['val'=>$invoiceSummary['unpaid'],'color'=>'bg-amber-500'],'Menunggu'=>['val'=>$invoiceSummary['pending'],'color'=>'bg-blue-500']] as $label => $data)
            <div class="flex items-center gap-2 text-sm">
                <span class="w-3 h-3 rounded-full {{ $data['color'] }} flex-shrink-0"></span>
                <span class="text-slate-600">{{ $label }}</span>
                <span class="ml-auto font-semibold text-slate-800">{{ $data['val'] }}</span>
            </div>
            @endforeach
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-6">
        <h3 class="font-semibold text-slate-800 mb-5">Penghuni Terbaik</h3>
        @if($topTenants->isEmpty())
        <div class="flex items-center justify-center h-32 text-slate-400 text-sm">Belum ada data</div>
        @else
        <div class="space-y-3">
            @foreach($topTenants as $i => $t)
            <div class="flex items-center gap-3">
                <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold {{ $i===0 ? 'bg-amber-400 text-amber-900' : 'bg-slate-200 text-slate-600' }}">{{ $i+1 }}</span>
                <img src="{{ $t->photo_url }}" alt="" class="w-8 h-8 rounded-full object-cover">
                <div class="flex-1">
                    <p class="text-sm font-medium text-slate-800">{{ $t->name }}</p>
                    <p class="text-xs text-slate-400">Kamar {{ $t->room->number }}</p>
                </div>
                <p class="text-sm font-bold text-emerald-600">Rp {{ number_format($t->paid_amount ?? 0,0,',','.') }}</p>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const monthlyData = @json($monthlyData);

// Annual bar chart
new Chart(document.getElementById('annualChart'), {
    type: 'bar',
    data: {
        labels: monthlyData.map(d => d.month),
        datasets: [{
            label: 'Pendapatan',
            data: monthlyData.map(d => d.revenue),
            backgroundColor: monthlyData.map((d,i) => i === {{ $month - 1 }} ? '#2563EB' : 'rgba(37,99,235,0.15)'),
            borderColor: monthlyData.map((d,i) => i === {{ $month - 1 }} ? '#1d4ed8' : '#2563EB'),
            borderWidth: 2,
            borderRadius: 8,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: {
                grid: { color: 'rgba(0,0,0,0.04)' },
                ticks: { callback: v => 'Rp '+Intl.NumberFormat('id-ID').format(v), font: { size: 10 } }
            },
            x: { grid: { display: false }, ticks: { font: { size: 11 } } }
        }
    }
});

// Status pie chart
new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: ['Lunas','Belum Bayar','Menunggu'],
        datasets: [{
            data: [{{ $invoiceSummary['paid'] }}, {{ $invoiceSummary['unpaid'] }}, {{ $invoiceSummary['pending'] }}],
            backgroundColor: ['#22C55E','#F59E0B','#3B82F6'],
            borderWidth: 0,
            hoverOffset: 6
        }]
    },
    options: { cutout: '68%', plugins: { legend: { display: false } } }
});
</script>
@endpush
