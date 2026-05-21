@extends('template.main')

@push('style')
<link rel="stylesheet" href="{{ asset('style/admin/dashboard.css') }}">
@endpush

@section('title', 'Dashboard')

@section('content')

{{-- Header --}}
<div class="header">
    <h1>Dashboard</h1>
    @auth
        <p>Hai {{ auth()->user()->name }}, Welcome to Cinta Rasa</p>
    @else
        <p>Hai Admin, Welcome to Cinta Rasa</p>
    @endauth
</div>

{{-- Stat Cards --}}
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

{{-- Chart Row --}}
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

{{-- Main Grid --}}
<div class="dashboard-grid">

    {{-- LEFT --}}
    <div class="left-panel">

        {{-- Status Meja --}}
        <div class="meja-status-section">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                <h2 style="margin:0; font-size:16px; font-weight:700; color:#3d2b1f;">Status Meja</h2>
                <button data-bs-toggle="modal" data-bs-target="#modalTambahMeja"
                    style="background:#9b632c; color:#fff; border:none; border-radius:10px; padding:8px 16px; font-size:13px; font-weight:600; cursor:pointer; display:flex; align-items:center; gap:6px;">
                    <i class="bi bi-plus-circle"></i> Tambah Meja
                </button>
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
                    <button class="btn-delete-meja"
                        onclick="deleteMeja('{{ $meja->no_meja }}', '{{ $mejaStatus }}')"
                        {{ $mejaStatus !== 'kosong' ? 'disabled' : '' }}
                        title="{{ $mejaStatus !== 'kosong' ? 'Meja sedang digunakan' : 'Hapus meja ini' }}">
                        <i class="bi bi-trash"></i>
                        {{ $mejaStatus !== 'kosong' ? 'Sedang digunakan' : 'Hapus Meja' }}
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
                            <button type="button" class="btn-update-status" data-id="{{ $order->id }}">Update</button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="order-card"><p>Tidak ada order masuk.</p></div>
            @endforelse
        </div>
    </div>

</div>

{{-- Modal Tambah Meja --}}
<div class="modal fade" id="modalTambahMeja" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px; padding:8px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Tambah Meja</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-3">
                <form method="POST" action="/admin/meja/store">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nomor Meja</label>
                        <input type="text" name="no_meja" class="form-control"
                               placeholder="Contoh: A1" required
                               style="border-radius:10px; padding:10px 14px;">
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal"
                                style="border-radius:10px;">Batal</button>
                        <button type="submit" class="btn px-4 text-white"
                                style="background:#9b632c; border-radius:10px;">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal Konfirmasi Hapus Meja --}}
<div class="modal fade" id="modalKonfirmasiHapus" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="border-radius:16px; padding:8px; text-align:center;">
            <div class="modal-body pt-4 pb-2">
                <div style="font-size:40px; color:#e74c3c; margin-bottom:12px;">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <h5 class="fw-bold" style="color:#3d2b1f; margin-bottom:8px;">Hapus Meja?</h5>
                <p style="color:#8a6a54; font-size:13px; margin-bottom:20px;">
                    Meja <strong id="konfirmasi-meja-no"></strong> akan dihapus permanen.<br>
                    Tindakan ini tidak bisa dibatalkan.
                </p>
                <div class="d-flex gap-2 justify-content-center mb-2">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal"
                            style="border-radius:10px; font-size:13px;">Batal</button>
                    <button type="button" id="btn-konfirmasi-hapus" class="btn px-4 text-white"
                            style="background:#e74c3c; border-radius:10px; font-size:13px;">
                        Ya, Hapus
                    </button>
                </div>
            </div>
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
// ─── Bar Chart: Penjualan Mingguan ───────────────────────────────────────────
var weeklyLabels = {!! json_encode($weeklyLabels ?? ['Sen','Sel','Rab','Kam','Jum','Sab','Min']) !!};
var weeklyData   = {!! json_encode($weeklyData   ?? [0,0,0,0,0,0,0]) !!};

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
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => 'Rp ' + ctx.parsed.y.toLocaleString('id-ID')
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
                    callback: v => 'Rp ' + (v >= 1000 ? (v / 1000) + 'rb' : v)
                }
            }
        }
    }
});

// ─── Donut Chart: Kategori Order ─────────────────────────────────────────────
var categoryData = {!! json_encode($categoryData ?? [58, 30, 12]) !!};

new Chart(document.getElementById('pieChart'), {
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
    if (!e.target.classList.contains('btn-update-status')) return;

    var id     = e.target.dataset.id;
    var form   = e.target.closest('.status-form');
    var select = form.querySelector('.status-select');
    var status = select.value;
    var token  = select.dataset.token;
    var btn    = e.target;

    btn.textContent = '...';
    btn.disabled    = true;

    fetch('/admin/orders/' + id + '/status', {
        method: 'POST',
        headers: {
            'Content-Type'           : 'application/json',
            'X-CSRF-TOKEN'           : token,
            'X-HTTP-Method-Override' : 'PUT'
        },
        body: JSON.stringify({ status: status })
    })
    .then(function (res) {
        if (res.ok) {
            var card     = btn.closest('.order-card');
            var badge    = card.querySelector('.order-status');
            var labelMap = { pending: 'Pending', confirmed: 'Confirmed', completed: 'Completed' };
            badge.textContent = labelMap[status];
            badge.className   = 'order-status ' + status;
            btn.textContent   = '✓';
            setTimeout(function () { btn.textContent = 'Update'; btn.disabled = false; }, 1500);
        } else {
            btn.textContent = 'Error';
            btn.disabled    = false;
        }
    })
    .catch(function () {
        btn.textContent = 'Error';
        btn.disabled    = false;
    });
});

// ─── Delete Meja ─────────────────────────────────────────────────────────────
var _mejaToDelete = null;

function deleteMeja(noMeja, status) {
    if (status !== 'kosong') {
        alert('Meja sedang digunakan, tidak bisa dihapus.');
        return;
    }

    // Isi nama meja di modal konfirmasi
    _mejaToDelete = noMeja;
    document.getElementById('konfirmasi-meja-no').textContent = noMeja;

    new bootstrap.Modal(document.getElementById('modalKonfirmasiHapus')).show();
}

document.getElementById('btn-konfirmasi-hapus').addEventListener('click', function () {
    if (!_mejaToDelete) return;

    var noMeja = _mejaToDelete;
    var btn    = this;

    btn.textContent = 'Menghapus...';
    btn.disabled    = true;

    fetch('/admin/meja/' + encodeURIComponent(noMeja) + '/delete', {
        method: 'POST',
        headers: {
            'Content-Type' : 'application/json',
            'X-CSRF-TOKEN' : '{{ csrf_token() }}'
        }
    })
    .then(res => res.json())
    .then(data => {
        // Tutup modal dulu
        var modalEl = document.getElementById('modalKonfirmasiHapus');
        bootstrap.Modal.getInstance(modalEl)?.hide();

        if (data.success) {
            // Hapus card dari DOM
            var box = document.querySelector('[data-meja="' + noMeja + '"]');
            if (box) {
                box.style.transition = 'opacity 0.3s, transform 0.3s';
                box.style.opacity    = '0';
                box.style.transform  = 'scale(0.85)';
                setTimeout(() => box.remove(), 300);
            }
        } else {
            alert(data.message || 'Gagal menghapus meja.');
        }

        btn.textContent = 'Ya, Hapus';
        btn.disabled    = false;
        _mejaToDelete   = null;
    })
    .catch(() => {
        alert('Terjadi kesalahan koneksi.');
        btn.textContent = 'Ya, Hapus';
        btn.disabled    = false;
    });
});

// ─── Polling Status Meja tiap 5 detik ────────────────────────────────────────
function pollMejaStatus() {
    fetch('/admin/meja/status')
        .then(res => res.json())
        .then(data => {
            data.forEach(meja => {
                var box = document.querySelector('[data-meja="' + meja.no_meja + '"]');
                if (!box) return;

                // Update class status
                box.classList.remove('meja-box-kosong', 'meja-box-pending', 'meja-box-aktif');
                box.classList.add('meja-box-' + meja.status);

                // Update teks status
                var statusText = box.querySelector('.status-text');
                if (statusText) {
                    statusText.textContent =
                        meja.status === 'pending' ? 'Memesan' :
                        meja.status === 'aktif'   ? 'Diproses' : 'Kosong';
                }

                // Update nama customer
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

                // Update barcode button
                var barcodeBtn = box.querySelector('.btn-barcode');
                if (barcodeBtn) {
                    barcodeBtn.setAttribute('onclick',
                        "showBarcode('" + meja.no_meja + "', '" + (meja.qr_uuid || '') + "')");
                }

                // Update delete button — disable jika meja aktif
                var deleteBtn = box.querySelector('.btn-delete-meja');
                if (deleteBtn) {
                    if (meja.status === 'kosong') {
                        deleteBtn.disabled  = false;
                        deleteBtn.title     = 'Hapus meja ini';
                        deleteBtn.innerHTML = '<i class="bi bi-trash"></i> Hapus Meja';
                        deleteBtn.setAttribute('onclick',
                            "deleteMeja('" + meja.no_meja + "', 'kosong')");
                    } else {
                        deleteBtn.disabled  = true;
                        deleteBtn.title     = 'Meja sedang digunakan';
                        deleteBtn.innerHTML = '<i class="bi bi-trash"></i> Sedang digunakan';
                        deleteBtn.setAttribute('onclick', '');
                    }
                }
            });
        })
        .catch(err => console.warn('Polling gagal:', err));
}

setInterval(pollMejaStatus, 5000);
</script>
@endpush