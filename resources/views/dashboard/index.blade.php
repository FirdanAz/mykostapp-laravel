@extends('layouts.app')
@section('title','Dashboard')
@section('page-title','Dashboard')

@section('content')
<div class="space-y-6">

{{-- ── Stats Cards ────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

    @php
    $cards = [
        ['label'=>'Total Kamar',      'value'=>$totalRooms,      'icon'=>'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', 'color'=>'blue',   'sub'=>"Lantai {$maintenanceRooms} maintenance"],
        ['label'=>'Kamar Terisi',     'value'=>$occupiedRooms,   'icon'=>'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',          'color'=>'indigo', 'sub'=>"{$activeTenants} penghuni aktif"],
        ['label'=>'Kamar Kosong',     'value'=>$availableRooms,  'icon'=>'M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z', 'color'=>'green', 'sub'=>'Siap disewa'],
        ['label'=>'Pendapatan Bulan Ini', 'value'=>'Rp '.number_format($monthlyRevenue,0,',','.'), 'icon'=>'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 8v1M12 3v1m0 16v1m9-9h-1M4 12H3','color'=>'emerald','sub'=>'Pembayaran terverifikasi'],
    ];
    @endphp

    @foreach($cards as $card)
    @php
    $colors = [
        'blue'   => ['bg'=>'bg-blue-50',   'icon'=>'bg-blue-600',   'text'=>'text-blue-600'],
        'indigo' => ['bg'=>'bg-indigo-50', 'icon'=>'bg-indigo-600', 'text'=>'text-indigo-600'],
        'green'  => ['bg'=>'bg-green-50',  'icon'=>'bg-green-600',  'text'=>'text-green-600'],
        'emerald'=> ['bg'=>'bg-emerald-50','icon'=>'bg-emerald-600','text'=>'text-emerald-600'],
    ];
    $c = $colors[$card['color']];
    @endphp
    <div class="bg-white rounded-2xl border border-slate-200 p-5 hover:shadow-md transition-shadow">
        <div class="flex items-start justify-between mb-4">
            <div class="{{ $c['bg'] }} p-2.5 rounded-xl">
                <svg class="w-5 h-5 {{ $c['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-slate-900">{{ $card['value'] }}</p>
        <p class="text-sm font-medium text-slate-600 mt-0.5">{{ $card['label'] }}</p>
        <p class="text-xs text-slate-400 mt-1">{{ $card['sub'] }}</p>
    </div>
    @endforeach
</div>

{{-- ── Alert Cards ────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    @if($unpaidInvoices > 0)
    <a href="{{ route('invoices.index', ['status'=>'unpaid']) }}"
       class="flex items-center gap-4 bg-amber-50 border border-amber-200 rounded-2xl p-4 hover:bg-amber-100 transition-colors">
        <div class="w-10 h-10 bg-amber-500 rounded-xl flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <div>
            <p class="font-semibold text-amber-800 text-sm">{{ $unpaidInvoices }} Tagihan Belum Dibayar</p>
            <p class="text-xs text-amber-600 mt-0.5">Klik untuk melihat detail</p>
        </div>
        <svg class="w-5 h-5 text-amber-500 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
    </a>
    @endif
    @if($activeComplaints > 0)
    <a href="{{ route('complaints.index', ['status'=>'new']) }}"
       class="flex items-center gap-4 bg-red-50 border border-red-200 rounded-2xl p-4 hover:bg-red-100 transition-colors">
        <div class="w-10 h-10 bg-red-500 rounded-xl flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
        </div>
        <div>
            <p class="font-semibold text-red-800 text-sm">{{ $activeComplaints }} Keluhan Aktif</p>
            <p class="text-xs text-red-600 mt-0.5">Perlu ditangani segera</p>
        </div>
        <svg class="w-5 h-5 text-red-500 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
    </a>
    @endif
</div>

{{-- ── Charts Row ──────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Revenue Chart --}}
    <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="font-semibold text-slate-800">Pendapatan Bulanan</h3>
                <p class="text-xs text-slate-400 mt-0.5">6 bulan terakhir</p>
            </div>
        </div>
        <canvas id="revenueChart" height="200"></canvas>
    </div>

    {{-- Occupancy Donut --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-6">
        <div class="mb-6">
            <h3 class="font-semibold text-slate-800">Tingkat Hunian</h3>
            <p class="text-xs text-slate-400 mt-0.5">Bulan ini</p>
        </div>
        <canvas id="occupancyChart" height="200"></canvas>
        <div class="mt-4 space-y-2">
            <div class="flex items-center justify-between text-sm">
                <span class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-blue-500"></span>Terisi</span>
                <span class="font-medium text-slate-700">{{ $occupiedRooms }}</span>
            </div>
            <div class="flex items-center justify-between text-sm">
                <span class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-green-400"></span>Tersedia</span>
                <span class="font-medium text-slate-700">{{ $availableRooms }}</span>
            </div>
            <div class="flex items-center justify-between text-sm">
                <span class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-amber-400"></span>Maintenance</span>
                <span class="font-medium text-slate-700">{{ $maintenanceRooms }}</span>
            </div>
        </div>
    </div>
</div>

{{-- ── Recent Tables ───────────────────────────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Recent Invoices --}}
    <div class="bg-white rounded-2xl border border-slate-200">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <h3 class="font-semibold text-slate-800 text-sm">Tagihan Terbaru</h3>
            <a href="{{ route('invoices.index') }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">Lihat semua →</a>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse($recentInvoices as $invoice)
            <a href="{{ route('invoices.show', $invoice) }}" class="flex items-center gap-3 px-6 py-3 hover:bg-slate-50 transition-colors">
                <div class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-800 truncate">{{ $invoice->tenant->name }}</p>
                    <p class="text-xs text-slate-400">{{ $invoice->invoice_number }}</p>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="text-sm font-semibold text-slate-800">{{ $invoice->formatted_amount }}</p>
                    @php
                    $sc = ['unpaid'=>'bg-amber-100 text-amber-700','pending_verification'=>'bg-blue-100 text-blue-700','paid'=>'bg-green-100 text-green-700','rejected'=>'bg-red-100 text-red-700','overdue'=>'bg-orange-100 text-orange-700'];
                    @endphp
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $sc[$invoice->status] ?? 'bg-slate-100 text-slate-600' }}">{{ $invoice->status_label }}</span>
                </div>
            </a>
            @empty
            <div class="px-6 py-8 text-center text-slate-400 text-sm">Belum ada tagihan</div>
            @endforelse
        </div>
    </div>

    {{-- Recent Complaints --}}
    <div class="bg-white rounded-2xl border border-slate-200">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <h3 class="font-semibold text-slate-800 text-sm">Keluhan Terbaru</h3>
            <a href="{{ route('complaints.index') }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">Lihat semua →</a>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse($recentComplaints as $complaint)
            <a href="{{ route('complaints.show', $complaint) }}" class="flex items-center gap-3 px-6 py-3 hover:bg-slate-50 transition-colors">
                <img src="{{ $complaint->tenant->photo_url }}" alt="" class="w-8 h-8 rounded-full object-cover flex-shrink-0">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-800 truncate">{{ $complaint->title }}</p>
                    <p class="text-xs text-slate-400">{{ $complaint->tenant->name }}</p>
                </div>
                @php
                $sc2 = ['new'=>'bg-blue-100 text-blue-700','in_progress'=>'bg-amber-100 text-amber-700','resolved'=>'bg-green-100 text-green-700','rejected'=>'bg-red-100 text-red-700'];
                @endphp
                <span class="text-xs px-2 py-0.5 rounded-full {{ $sc2[$complaint->status] ?? 'bg-slate-100 text-slate-600' }} flex-shrink-0">{{ $complaint->status_label }}</span>
            </a>
            @empty
            <div class="px-6 py-8 text-center text-slate-400 text-sm">Tidak ada keluhan aktif</div>
            @endforelse
        </div>
    </div>
</div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Revenue Chart
const revenueData = @json($revenueChart);
new Chart(document.getElementById('revenueChart'), {
    type: 'bar',
    data: {
        labels: revenueData.map(d => d.month),
        datasets: [{
            label: 'Pendapatan',
            data: revenueData.map(d => d.total),
            backgroundColor: 'rgba(37,99,235,0.1)',
            borderColor: '#2563EB',
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
                grid: { color: 'rgba(0,0,0,0.05)' },
                ticks: {
                    callback: v => 'Rp '+Intl.NumberFormat('id-ID').format(v),
                    font: { size: 11 }
                }
            },
            x: { grid: { display: false }, ticks: { font: { size: 11 } } }
        }
    }
});

// Occupancy Donut
new Chart(document.getElementById('occupancyChart'), {
    type: 'doughnut',
    data: {
        labels: ['Terisi','Tersedia','Maintenance'],
        datasets: [{
            data: [{{ $occupiedRooms }}, {{ $availableRooms }}, {{ $maintenanceRooms }}],
            backgroundColor: ['#3B82F6','#4ADE80','#FBBF24'],
            borderWidth: 0,
            hoverOffset: 4
        }]
    },
    options: {
        cutout: '72%',
        plugins: { legend: { display: false } }
    }
});
</script>
@endpush
