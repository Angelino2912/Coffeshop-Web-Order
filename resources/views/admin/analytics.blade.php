@extends('template.main')
 
@push('style')
<link rel="stylesheet" href="{{ asset('style/admin/analytics.css') }}">
@endpush
 
@section('title', 'Analytics')
 
@section('content')
 
<div class="header">
    <h1>Analytics</h1>
    <p>Statistik penjualan Cinta Rasa</p>
</div>
 
{{-- Filter Periode --}}
<div class="filter-row">
    <button class="filter-btn active" onclick="setFilter('week', this)">7 Hari Terakhir</button>
    <button class="filter-btn" onclick="setFilter('month', this)">30 Hari Terakhir</button>
</div>
 
{{-- Stat Cards --}}
<div class="stat-row">
    <div class="stat-card">
        <div class="stat-label">Total Pendapatan</div>
        <div class="stat-value" id="statPendapatan">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Order</div>
        <div class="stat-value" id="statOrder">{{ $totalOrder }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Rata-rata per Order</div>
        <div class="stat-value" id="statRata">Rp {{ number_format($rataRata, 0, ',', '.') }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Item Terjual</div>
        <div class="stat-value" id="statItem">{{ $totalItem }}</div>
    </div>
</div>
 
{{-- Charts --}}
<div class="chart-row">
    <div class="chart-card wide">
        <div class="chart-title">Grafik Penjualan</div>
        <div class="bar-chart-wrap">
            <canvas id="salesChart"></canvas>
        </div>
    </div>
    <div class="chart-card">
        <div class="chart-title">Kategori</div>
        <div class="pie-wrap">
            <canvas id="pieChart" width="130" height="130"></canvas>
            <div class="pie-legend">
                <div class="leg-item"><span class="leg-dot" style="background:#9b632c"></span>Makanan</div>
                <div class="leg-item"><span class="leg-dot" style="background:#5DCAA5"></span>Minuman</div>
                <div class="leg-item"><span class="leg-dot" style="background:#EF9F27"></span>Snack</div>
            </div>
        </div>
    </div>
</div>
 
{{-- Bottom Grid --}}
<div class="bottom-grid">
 
    {{-- Top Menu --}}
    <div class="panel-card">
        <div class="panel-title">🏆 Top Menu Terlaris</div>
        <div class="top-menu-list">
            @forelse($topMenus as $i => $menu)
            <div class="top-menu-item">
                <div class="top-menu-rank rank-{{ $i + 1 }}">{{ $i + 1 }}</div>
                <div class="top-menu-info">
                    <div class="top-menu-name">{{ $menu->name }}</div>
                    <div class="top-menu-cat">{{ ucfirst($menu->category) }}</div>
                </div>
                <div class="top-menu-qty">{{ $menu->total_qty }} terjual</div>
            </div>
            @empty
            <p class="empty-text">Belum ada data</p>
            @endforelse
        </div>
    </div>
 
    {{-- Riwayat Order --}}
    <div class="panel-card wide">
        <div class="panel-title">📋 Riwayat Order</div>
        <div class="table-wrap">
            <table class="order-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Pelanggan</th>
                        <th>Meja</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->customer_name }}</td>
                        <td>{{ $order->table_number }}</td>
                        <td>Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                        <td><span class="badge-status {{ $order->status }}">{{ ucfirst($order->status) }}</span></td>
                        <td>{{ $order->created_at->format('d M, H:i') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="empty-text">Belum ada order</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
 
</div>
 
@endsection
 
@push('custom_script')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>

var weeklyLabels = {!! json_encode($weeklyLabels) !!};
var weeklyData   = {!! json_encode($weeklyData) !!};
var monthlyLabels = {!! json_encode($monthlyLabels) !!};
var monthlyData   = {!! json_encode($monthlyData) !!};
var categoryData  = {!! json_encode($categoryData) !!};
 

var salesCtx = document.getElementById('salesChart').getContext('2d');
var salesChart = new Chart(salesCtx, {
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
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: function(ctx) {
                        return 'Rp ' + ctx.parsed.y.toLocaleString('id-ID');
                    }
                }
            }
        },
        scales: {
            x: {
                grid: { display: false },
                ticks: { color: '#8a6a54', font: { size: 11 } }
            },
            y: {
                grid: { color: 'rgba(155,99,44,0.08)' },
                ticks: {
                    color: '#8a6a54',
                    font: { size: 10 },
                    callback: function(v) {
                        return 'Rp ' + (v >= 1000000 ? (v/1000000)+'jt' : v >= 1000 ? (v/1000)+'rb' : v);
                    }
                }
            }
        }
    }
});
 

new Chart(document.getElementById('pieChart').getContext('2d'), {
    type: 'doughnut',
    data: {
        labels: ['Makanan', 'Minuman', 'Snack'],
        datasets: [{
            data: categoryData,
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
 

function setFilter(period, el) {
    // Toggle button active
    document.querySelectorAll('.filter-btn').forEach(function(b) { b.classList.remove('active'); });
    el.classList.add('active');
 
    // Update chart
    if (period === 'week') {
        salesChart.data.labels = weeklyLabels;
        salesChart.data.datasets[0].data = weeklyData;
    } else {
        salesChart.data.labels = monthlyLabels;
        salesChart.data.datasets[0].data = monthlyData;
    }
    salesChart.update();
}
</script>
@endpush