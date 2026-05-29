@extends('template.main')

@push('style')
<link rel="stylesheet" href="{{ asset('style/admin/dashboard.css') }}">
@endpush

@section('title', 'Dashboard')

@section('content')
<div class="header">
    <h1>Dashboard</h1>
    <p>Ringkasan aktivitas Cafe Cinta Rasa.</p>
</div>

<div class="stat-row">
    <div class="stat-card">
        <div class="stat-label">Total order hari ini</div>
        <div class="stat-value">{{ $ordersToday->count() }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Pendapatan hari ini</div>
        <div class="stat-value">Rp {{ number_format($ordersToday->sum('total'), 0, ',', '.') }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Meja aktif</div>
        <div class="stat-value">
            @php
                $mejaAktif = \App\Models\Order::whereIn('status', ['pending', 'confirmed'])
                    ->distinct('table_number')
                    ->count('table_number');
            @endphp
            {{ $mejaAktif }} / {{ $mejas->count() }}
        </div>
    </div>
</div>

<div class="chart-row">
    <div class="chart-card">
        <div class="chart-header-row">
            <div class="chart-title">Penjualan Mingguan</div>
            <a href="{{ route('admin.analytics') }}" class="chart-link-btn">
                <i class="bi bi-graph-up-arrow"></i> Lihat Analytics
            </a>
        </div>
        <div class="bar-chart-wrap">
            <canvas id="barChart"></canvas>
        </div>
    </div>

    <div class="chart-card">
        <div class="chart-title">Kategori Order</div>
        <div class="pie-wrap">
            <canvas id="pieChart" width="140" height="140"></canvas>
            <div class="pie-legend">
                <div class="leg-item"><span class="leg-dot" style="background:#9b632c"></span>Makanan</div>
                <div class="leg-item"><span class="leg-dot" style="background:#5DCAA5"></span>Minuman</div>
                <div class="leg-item"><span class="leg-dot" style="background:#EF9F27"></span>Snack</div>
            </div>
        </div>
    </div>
</div>

<div class="dashboard-grid">
    <div class="left-panel" style="grid-column: 1 / -1;">
        <div class="meja-status-section">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                <h2 style="margin:0; font-size:16px; font-weight:700; color:#3d2b1f;">Status Meja</h2>
                <a href="/admin/manajemen-meja"
                   style="background:#9b632c; color:#fff; border:none; border-radius:10px; padding:8px 16px; font-size:13px; font-weight:600; text-decoration:none;">
                    Manajemen Meja
                </a>
            </div>

            <div class="meja-status-grid">
                @foreach($mejas as $meja)
                    @php
                        $activeOrder = \App\Models\Order::where('table_number', $meja->no_meja)
                            ->whereIn('status', ['pending', 'confirmed'])
                            ->latest()
                            ->first();
                        $mejaStatus = $activeOrder ? ($activeOrder->status === 'pending' ? 'pending' : 'aktif') : 'kosong';
                    @endphp
                    <div class="meja-status-box meja-box-{{ $mejaStatus }}">
                        <div class="meja-status-no">{{ $meja->no_meja }}</div>
                        <div class="meja-status-label">
                            <span class="status-dot"></span>
                            <span class="status-text">
                                @if($mejaStatus === 'pending') Memesan
                                @elseif($mejaStatus === 'aktif') Diproses
                                @else Kosong
                                @endif
                            </span>
                        </div>
                        <div class="meja-cust-name" @if($mejaStatus === 'kosong') style="display:none" @endif>
                            {{ $activeOrder?->customer_name ?? '' }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

</div>
@endsection

@push('custom_script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
var weeklyLabels = {!! json_encode($weeklyLabels ?? ['Sen','Sel','Rab','Kam','Jum','Sab','Min']) !!};
var weeklyData = {!! json_encode($weeklyData ?? [0,0,0,0,0,0,0]) !!};

new Chart(document.getElementById('barChart'), {
    type: 'bar',
    data: {
        labels: weeklyLabels,
        datasets: [{
            label: 'Penjualan',
            data: weeklyData,
            backgroundColor: '#9b632c',
            borderRadius: 6,
            borderSkipped: false
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display: false }, ticks: { color: '#8a6a54', font: { size: 11 } } },
            y: {
                grid: { color: 'rgba(155,99,44,0.08)' },
                ticks: {
                    color: '#8a6a54',
                    font: { size: 10 },
                    callback: v => 'Rp ' + (v >= 1000 ? (v / 1000) + 'rb' : v)
                }
            }
        }
    }
});

new Chart(document.getElementById('pieChart'), {
    type: 'doughnut',
    data: {
        labels: ['Makanan', 'Minuman', 'Snack'],
        datasets: [{
            data: {!! json_encode($categoryData ?? [0,0,0]) !!},
            backgroundColor: ['#9b632c', '#5DCAA5', '#EF9F27'],
            borderWidth: 0,
            hoverOffset: 4
        }]
    },
    options: {
        cutout: '55%',
        plugins: { legend: { display: false } },
        responsive: false
    }
});
</script>
@endpush
