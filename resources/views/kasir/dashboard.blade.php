@extends('template.main-kasir')

@push('style')
<link rel="stylesheet" href="{{ asset('style/kasir/dashboard.css') }}">
@endpush

@section('title', 'Dashboard Kasir')

@section('content')

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

{{-- Main Grid --}}
<div class="dashboard-grid">

    {{-- LEFT --}}
    <div class="left-panel">

        {{-- Status Meja --}}
        <div class="meja-status-section">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                <h2 style="margin:0; font-size:16px; font-weight:700; color:#3d2b1f;">Status Meja</h2>
                <form method="POST" action="{{ route('kasir.meja.generate-qr') }}">
                    @csrf
                    <button type="submit"
                        style="background:#9b632c; color:#fff; border:none; border-radius:10px; padding:8px 16px; font-size:13px; font-weight:600; cursor:pointer; display:flex; align-items:center; gap:6px;">
                        <i class="bi bi-qr-code"></i> Generate QR
                    </button>
                </form>
            </div>

            <div class="meja-status-grid" id="mejaStatusGrid">
                @foreach($mejas as $meja)
                @php
                    $activeOrder = \App\Models\Order::where('table_number', $meja->no_meja)
                        ->whereIn('status', ['pending', 'confirmed'])
                        ->latest()->first();

                    if ($activeOrder?->status === 'confirmed') {
                        $mejaStatus = 'aktif';
                    } elseif ($activeOrder?->status === 'pending') {
                        $mejaStatus = 'pending';
                    } else {
                        $mejaStatus = 'kosong';
                    }
                @endphp
                <div class="meja-status-box meja-box-{{ $mejaStatus }}"
                     data-meja="{{ $meja->no_meja }}"
                     data-uuid="{{ $meja->qr_uuid }}">
                    <div class="meja-status-no">{{ $meja->no_meja }}</div>
                    <div class="meja-status-label">
                        <span class="status-dot"></span>
                        <span class="status-text">
                            @if($mejaStatus === 'pending')   Memesan
                            @elseif($mejaStatus === 'aktif') Diproses
                            @else Kosong
                            @endif
                        </span>
                    </div>
                    <div class="meja-cust-name" @if($mejaStatus === 'kosong') style="display:none" @endif>
                        {{ $mejaStatus !== 'kosong' ? ($activeOrder?->customer_name ?? '—') : '' }}
                    </div>
                    <button class="btn-barcode"
                        onclick="showBarcode('{{ $meja->no_meja }}', '{{ $meja->qr_uuid }}')">
                        <i class="bi bi-qr-code"></i> Tampilkan barcode
                    </button>
                </div>
                @endforeach
            </div>
        </div>

    </div>

    {{-- RIGHT: Order Masuk --}}
    <div class="right-panel">
        <div class="order-list-section">
            <h2>Order Masuk</h2>
            @forelse($orders as $order)
                <div class="order-card">
                    <div class="order-card-header">
                        <div>
                            <h2>{{ $order->customer_name }}</h2>
                            <p class="order-meta">Meja {{ $order->table_number }} &bullet; {{ $order->created_at->format('d M Y H:i') }}</p>
                        </div>
                        <span class="order-status {{ $order->status }}">{{ ucfirst($order->status) }}</span>
                    </div>

                    @if($order->items->count())
                    <div class="order-items">
                        <h3>Pesanan</h3>
                        <ul>
                            @foreach($order->items as $item)
                                <li>
                                    <span class="item-name">{{ $item->menu?->name ?? 'Menu tidak tersedia' }}</span>
                                    <span class="item-qty">x{{ $item->quantity }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    @if($order->note)
                    <div class="order-note">
                        <strong>Catatan:</strong> {{ $order->note }}
                    </div>
                    @endif

                    <div class="order-footer">
                        <div class="order-total">Rp {{ number_format($order->total) }}</div>
                        <div class="status-form">
                            <select class="status-select"
                                    data-id="{{ $order->id }}"
                                    data-token="{{ csrf_token() }}">
                                <option value="pending"   {{ $order->status === 'pending'   ? 'selected' : '' }}>Pending</option>
                                <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>Diproses</option>
                                <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Selesai</option>
                            </select>
                            <button type="button" class="btn-update-status" data-id="{{ $order->id }}">
                                <span class="button-text">Update</span>
                                <span class="button-spinner" aria-label="Memuat"></span>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="order-card"><p>Tidak ada order masuk.</p></div>
            @endforelse
        </div>
    </div>

</div>

{{-- Modal Barcode --}}
<div class="modal fade" id="modalBarcode" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content barcode-modal">
            <div class="modal-header barcode-modal-header">
                <h5 class="modal-title" id="barcode-modal-title">QR Code Meja</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="text-align:center; padding:24px;">
                <p class="barcode-sub">Scan untuk mulai memesan</p>
                <div class="qr-output-wrap">
                    <div id="qrcode-output"></div>
                </div>
                <p class="qr-uuid-text" id="qr-uuid-display"></p>
                <div class="barcode-actions">
                    <button class="btn-tutup" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('custom_script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>

// ─── Chart Bar Mingguan ───────────────────────────────────────────────────────
const barCanvas = document.getElementById('barChart');
if (barCanvas) {
const barCtx = barCanvas.getContext('2d');
new Chart(barCtx, {
    type: 'bar',
    data: {
        labels: @json($weeklyLabels),
        datasets: [{
            label: 'Penjualan',
            data: @json($weeklyData),
            backgroundColor: '#9b632c',
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
    }
});
}

// ─── Chart Pie Kategori ───────────────────────────────────────────────────────
const pieCanvas = document.getElementById('pieChart');
if (pieCanvas) {
const pieCtx = pieCanvas.getContext('2d');
new Chart(pieCtx, {
    type: 'doughnut',
    data: {
        labels: ['Makanan', 'Minuman', 'Snack'],
        datasets: [{
            data: @json($categoryData),
            backgroundColor: ['#9b632c', '#5DCAA5', '#EF9F27'],
        }]
    },
    options: {
        responsive: false,
        plugins: { legend: { display: false } }
    }
});
}

// ─── Barcode Modal ────────────────────────────────────────────────────────────
function showBarcode(noMeja, uuid) {
    document.getElementById('barcode-modal-title').textContent = 'QR Code Meja ' + noMeja;
    document.getElementById('qr-uuid-display').textContent     = uuid || '';

    var output = document.getElementById('qrcode-output');
    output.innerHTML = '';

    if (!uuid) {
        output.innerHTML = '<p style="color:red;">QR belum digenerate untuk meja ini.</p>';
    } else {
        new QRCode(output, {
            text: window.location.origin + '/table/' + uuid,
            width: 180, height: 180,
            colorDark: '#3d2b1f', colorLight: '#ffffff'
        });
    }

    new bootstrap.Modal(document.getElementById('modalBarcode')).show();
}

// ─── Update Status Order via AJAX ─────────────────────────────────────────────
document.addEventListener('click', function (e) {
    var btn = e.target.closest('.btn-update-status');
    if (!btn) return;

    var id     = btn.dataset.id;
    var form   = btn.closest('.status-form');
    var select = form.querySelector('.status-select');
    var status = select.value;
    var token  = select.dataset.token;

    btn.classList.add('loading');
    btn.disabled = true;

    fetch('/kasir/orders/' + id + '/status', {
        method: 'POST',
        headers: {
            'Content-Type'           : 'application/json',
            'Accept'                 : 'application/json',
            'X-Requested-With'       : 'XMLHttpRequest',
            'X-CSRF-TOKEN'           : token,
            'X-HTTP-Method-Override' : 'PUT'
        },
        body: JSON.stringify({ status: status })
    })
    .then(function (res) { return res.json(); })
    .then(function (data) {
        if (data.success) {
            var card  = btn.closest('.order-card');
            var badge = card.querySelector('.order-status');
            badge.textContent = data.status_label;
            badge.className   = 'order-status ' + data.status;
        } else {
            alert('Status belum berhasil diupdate.');
        }
    })
    .catch(function () {
        alert('Status belum berhasil diupdate. Coba lagi sebentar.');
    })
    .finally(function () {
        btn.classList.remove('loading');
        btn.disabled = false;
    });
});

// ─── Delete Meja ─────────────────────────────────────────────────────────────
// ─── Polling Status Meja tiap 5 detik ────────────────────────────────────────
function pollMejaStatus() {
    fetch('/kasir/meja/status')
        .then(res => res.json())
        .then(data => {
            data.forEach(meja => {
                var box = document.querySelector('[data-meja="' + meja.no_meja + '"]');
                if (!box) return;

                box.classList.remove('meja-box-kosong', 'meja-box-pending', 'meja-box-aktif');
                box.classList.add('meja-box-' + meja.status);

                var statusText = box.querySelector('.status-text');
                if (statusText) {
                    statusText.textContent =
                        meja.status === 'pending' ? 'Memesan' :
                        meja.status === 'aktif'   ? 'Diproses' : 'Kosong';
                }

                var custName = box.querySelector('.meja-cust-name');
                if (custName) {
                    if (meja.customer_name && meja.status !== 'kosong') {
                        custName.textContent   = meja.customer_name;
                        custName.style.display = '';
                    } else {
                        custName.textContent   = '';
                        custName.style.display = 'none';
                    }
                }

                var barcodeBtn = box.querySelector('.btn-barcode');
                if (barcodeBtn) {
                    barcodeBtn.setAttribute('onclick',
                        "showBarcode('" + meja.no_meja + "', '" + (meja.qr_uuid || '') + "')");
                }

            });
        })
        .catch(err => console.warn('Polling gagal:', err));
}

setInterval(pollMejaStatus, 5000);
</script>
@endpush
