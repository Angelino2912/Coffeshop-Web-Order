@extends('template.main-kasir')

@push('style')
<style>
.orders-page {
    padding: 24px;
    min-height: calc(100vh - 60px);
    background: var(--cream, #FAF7F2);
    overflow-y: auto;
    flex: 1;
}
.orders-header { margin-bottom: 20px; }
.orders-title  { font-size: 20px; font-weight: 700; color: var(--text, #1A1A1A); }
.orders-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 14px;
    margin-bottom: 20px;
}
.stat-card {
    background: #fff;
    border-radius: 12px;
    border: 1px solid var(--border, #E8E0D5);
    padding: 14px 18px;
    display: flex;
    align-items: center;
    gap: 12px;
}
.stat-icon {
    width: 40px; height: 40px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; flex-shrink: 0;
}
.si-green  { background: #E8F5E9; color: #43A047; }
.si-yellow { background: #FFF8E1; color: #F59E0B; }
.si-blue   { background: #E3F2FD; color: #1E88E5; }
.si-brown  { background: #FDF0E6; color: var(--brown, #7B4F2E); }
.stat-value { font-size: 18px; font-weight: 700; color: var(--text, #1A1A1A); }
.stat-label { font-size: 11px; color: var(--text3, #999); font-weight: 500; }
.orders-filters {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 20px;
    align-items: center;
}
.filter-input, .filter-select {
    border: 1px solid var(--border, #E8E0D5);
    border-radius: 10px;
    padding: 8px 14px;
    font-family: inherit;
    font-size: 13px;
    background: #fff;
    outline: none;
    color: var(--text, #1a1a1a);
}
.filter-input:focus, .filter-select:focus { border-color: var(--green, #2D5016); }
.filter-input { min-width: 200px; }
.filter-select { min-width: 140px; }
.btn-filter {
    background: var(--green, #2D5016); color: #fff;
    border: none; border-radius: 10px;
    padding: 8px 18px; font-family: inherit;
    font-size: 13px; font-weight: 600; cursor: pointer;
}
.btn-filter:hover { background: var(--green2, #3a6b1e); }
.btn-reset {
    background: #FAF7F2; color: var(--text2, #555);
    border: 1px solid var(--border, #E8E0D5);
    border-radius: 10px; padding: 8px 18px;
    font-size: 13px; font-weight: 600;
    text-decoration: none; display: inline-flex; align-items: center;
}
.orders-list { display: flex; flex-direction: column; gap: 14px; }
.order-card {
    background: #fff;
    border-radius: 14px;
    border: 1px solid var(--border, #E8E0D5);
    padding: 18px 20px;
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 16px;
    transition: box-shadow .2s;
}
.order-card:hover { box-shadow: 0 2px 12px rgba(0,0,0,.06); }
.order-card-top {
    display: flex; align-items: center;
    gap: 10px; margin-bottom: 4px; flex-wrap: wrap;
}
.order-id       { font-size: 12px; font-weight: 700; color: var(--text3, #999); letter-spacing: .04em; }
.order-customer { font-size: 15px; font-weight: 700; color: var(--text, #1A1A1A); }
.order-meta {
    font-size: 12px; color: var(--text3, #999);
    margin-bottom: 10px; display: flex; gap: 14px; flex-wrap: wrap;
}
.order-meta i { margin-right: 3px; }
.order-items { display: flex; flex-wrap: wrap; gap: 6px; }
.item-chip {
    background: #FAF7F2;
    border: 1px solid var(--border, #E8E0D5);
    border-radius: 20px; padding: 3px 10px;
    font-size: 11px; color: var(--text2, #555); font-weight: 500;
}
.order-card-right {
    display: flex; flex-direction: column;
    align-items: flex-end; justify-content: space-between;
    min-width: 160px;
}
.order-total { font-size: 17px; font-weight: 700; color: var(--amber, #BA7517); }
.order-controls {
    display: flex; gap: 6px; align-items: center;
    margin-top: 8px; flex-wrap: wrap; justify-content: flex-end;
}
.order-status-select {
    border: 1px solid var(--border, #E8E0D5);
    border-radius: 8px; padding: 5px 8px;
    font-family: inherit; font-size: 12px;
    outline: none; background: #fff; cursor: pointer;
}
.btn-update {
    background: var(--green, #2D5016); color: #fff;
    border: none; border-radius: 8px;
    padding: 5px 14px; font-size: 12px;
    font-weight: 600; font-family: inherit; cursor: pointer;
}
.btn-update:hover { background: var(--green2, #3a6b1e); }
.btn-bayar {
    background: #F59E0B; color: #fff;
    border: none; border-radius: 8px;
    padding: 5px 14px; font-size: 12px;
    font-weight: 600; font-family: inherit;
    cursor: pointer; text-decoration: none;
    display: inline-flex; align-items: center; gap: 4px;
}
.btn-bayar:hover { background: #D97706; color: #fff; }
.btn-hapus {
    background: #fff; color: #E53935;
    border: 1px solid #FFCDD2;
    border-radius: 8px; padding: 5px 10px;
    font-size: 12px; font-weight: 600;
    font-family: inherit; cursor: pointer;
    display: inline-flex; align-items: center; gap: 4px;
    transition: background .15s, border-color .15s;
}
.btn-hapus:hover { background: #FFEBEE; border-color: #E53935; }
.label-sudah-bayar {
    font-size: 11px; font-weight: 600;
    color: #1E88E5; background: #E3F2FD;
    border-radius: 8px; padding: 5px 10px;
    display: inline-flex; align-items: center; gap: 4px;
    white-space: nowrap;
}
.paid-info {
    font-size: 11px; color: #43A047;
    font-weight: 600; margin-top: 4px;
    text-align: right; line-height: 1.7;
}
.status-badge { font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 20px; }
.badge-pending   { background: #FFF8E1; color: #F59E0B; }
.badge-confirmed { background: #E3F2FD; color: #1E88E5; }
.badge-completed { background: #E8F5E9; color: #43A047; }
.badge-cancelled { background: #FFEBEE; color: #E53935; }
.type-badge {
    font-size: 10px; font-weight: 600;
    padding: 2px 8px; border-radius: 20px;
    background: #EDE8E0; color: var(--text2, #555);
}
.orders-empty {
    text-align: center; padding: 60px 20px;
    color: var(--text3, #999); font-size: 14px;
    background: #fff; border-radius: 14px;
    border: 1px solid var(--border, #E8E0D5);
}
.orders-empty i { font-size: 40px; display: block; margin-bottom: 10px; opacity: .5; }
.orders-pagination { margin-top: 24px; display: flex; flex-direction: column; align-items: center; }

/* ── Modal Konfirmasi Hapus ── */
.del-modal-wrap {
    display: none;
    position: fixed; inset: 0;
    background: rgba(0,0,0,.5);
    z-index: 999999;
    align-items: center;
    justify-content: center;
}
.del-modal-wrap.on { display: flex; }
.del-modal {
    background: #fff;
    border-radius: 16px;
    padding: 28px 28px 22px;
    max-width: 380px; width: 90%;
    box-shadow: 0 8px 40px rgba(0,0,0,.2);
    animation: delModalIn .18s ease;
}
@keyframes delModalIn {
    from { transform: scale(.93); opacity: 0; }
    to   { transform: scale(1);   opacity: 1; }
}
.del-modal-icon {
    width: 52px; height: 52px;
    background: #FFEBEE; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 24px; color: #E53935;
    margin: 0 auto 14px;
}
.del-modal-title {
    font-size: 16px; font-weight: 700;
    color: #1A1A1A; text-align: center; margin-bottom: 6px;
}
.del-modal-desc {
    font-size: 13px; color: #999;
    text-align: center; margin-bottom: 22px; line-height: 1.6;
}
.del-modal-actions { display: flex; gap: 10px; }
.del-btn-cancel {
    flex: 1; padding: 10px;
    border: 1px solid #E8E0D5;
    border-radius: 10px; background: #FAF7F2;
    color: #555; font-size: 13px;
    font-weight: 600; font-family: inherit; cursor: pointer;
}
.del-btn-cancel:hover { background: #EDE8E0; }
.del-btn-ok {
    flex: 1; padding: 10px;
    border: none; border-radius: 10px;
    background: #E53935; color: #fff;
    font-size: 13px; font-weight: 700;
    font-family: inherit; cursor: pointer;
}
.del-btn-ok:hover { background: #C62828; }
.del-btn-ok:disabled { background: #ef9a9a; cursor: not-allowed; }

/* ── Toast ── */
.toast-k {
    position: fixed; bottom: 24px; right: 24px;
    padding: 12px 20px; border-radius: 10px;
    font-size: 13px; font-weight: 600; color: #fff;
    z-index: 9999999;
    box-shadow: 0 4px 20px rgba(0,0,0,.15);
    transform: translateY(60px); opacity: 0;
    transition: all .3s ease;
    display: flex; align-items: center; gap: 8px;
    pointer-events: none;
}
.toast-k.show { transform: translateY(0); opacity: 1; }
.toast-k.ts   { background: #43A047; }
.toast-k.te   { background: #E53935; }
</style>
@endpush

@section('title', 'Riwayat Order')

@section('content')
<div class="orders-page">

    <div class="orders-header">
        <div class="orders-title">Riwayat Order</div>
    </div>

    @if(session('success'))
    <div style="background:#E8F5E9;color:#2E7D32;border-radius:10px;padding:12px 16px;font-size:13px;margin-bottom:16px;font-weight:600;">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    @if(session('deleted'))
    <div style="background:#FFEBEE;color:#C62828;border-radius:10px;padding:12px 16px;font-size:13px;margin-bottom:16px;font-weight:600;">
        <i class="bi bi-trash3"></i> {{ session('deleted') }}
    </div>
    @endif

    {{-- Stats --}}
    <div class="orders-stats">
        <div class="stat-card">
            <div class="stat-icon si-brown"><i class="bi bi-receipt"></i></div>
            <div>
                <div class="stat-value">{{ $totalOrders }}</div>
                <div class="stat-label">Total Order</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon si-green"><i class="bi bi-check-circle"></i></div>
            <div>
                <div class="stat-value">{{ $completedCount }}</div>
                <div class="stat-label">Selesai</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon si-yellow"><i class="bi bi-clock-history"></i></div>
            <div>
                <div class="stat-value">{{ $pendingCount }}</div>
                <div class="stat-label">Pending</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon si-blue"><i class="bi bi-cash-stack"></i></div>
            <div>
                <div class="stat-value" style="font-size:14px;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
                <div class="stat-label">Total Pendapatan</div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('kasir.orders') }}" class="orders-filters">
        <input class="filter-input" type="text" name="search"
               placeholder="Cari nama / meja..." value="{{ request('search') }}">
        <select class="filter-select" name="status">
            <option value="">Semua Status</option>
            <option value="pending"   {{ request('status')==='pending'   ?'selected':'' }}>Pending</option>
            <option value="confirmed" {{ request('status')==='confirmed' ?'selected':'' }}>Diproses</option>
            <option value="completed" {{ request('status')==='completed' ?'selected':'' }}>Selesai</option>
            <option value="cancelled" {{ request('status')==='cancelled' ?'selected':'' }}>Dibatalkan</option>
        </select>
        <select class="filter-select" name="order_type">
            <option value="">Semua Tipe</option>
            <option value="Makan di Sini" {{ request('order_type')==='Makan di Sini'?'selected':'' }}>Makan di Sini</option>
            <option value="Bawa Pulang"   {{ request('order_type')==='Bawa Pulang'  ?'selected':'' }}>Bawa Pulang</option>
        </select>
        <input class="filter-input" type="date" name="date"
               value="{{ request('date') }}" style="min-width:160px;">
        <button type="submit" class="btn-filter">
            <i class="bi bi-search"></i> Filter
        </button>
        @if(request()->hasAny(['search','status','order_type','date']))
        <a href="{{ route('kasir.orders') }}" class="btn-reset">
            <i class="bi bi-x-circle" style="margin-right:4px;"></i> Reset
        </a>
        @endif
    </form>

    {{-- List --}}
    <div class="orders-list" id="ordersList">
        @forelse($orders as $order)
        @php
            $sc = match($order->status) {
                'pending'   => 'badge-pending',
                'confirmed' => 'badge-confirmed',
                'completed' => 'badge-completed',
                'cancelled' => 'badge-cancelled',
                default     => 'badge-pending',
            };
            $sl = match($order->status) {
                'pending'   => 'Pending',
                'confirmed' => 'Diproses',
                'completed' => 'Selesai',
                'cancelled' => 'Dibatalkan',
                default     => 'Pending',
            };
            $isBawaPulang = empty($order->table_number);
        @endphp
        <div class="order-card" id="order-card-{{ $order->id }}">
            <div>
                <div class="order-card-top">
                    <span class="order-id">#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</span>
                    <span class="status-badge {{ $sc }}">{{ $sl }}</span>
                    <span class="type-badge">{{ $isBawaPulang ? 'Bawa Pulang' : ($order->order_type ?? 'Makan di Sini') }}</span>
                </div>
                <div class="order-customer">{{ $order->customer_name }}</div>
                <div class="order-meta">
                    <span>
                        <i class="bi bi-grid-3x3"></i>
                        {{ $order->table_number ? 'Meja ' . $order->table_number : 'Bungkus / Bawa Pulang' }}
                    </span>
                    <span>
                        <i class="bi bi-calendar3"></i>
                        {{ \Carbon\Carbon::parse($order->created_at)->format('d M Y, H:i') }}
                    </span>
                </div>
                <div class="order-items">
                    @foreach($order->items as $item)
                    <span class="item-chip">{{ $item->menu->name ?? '-' }} x{{ $item->quantity }}</span>
                    @endforeach
                </div>
            </div>

            <div class="order-card-right">
                <div class="order-total">Rp {{ number_format($order->total, 0, ',', '.') }}</div>

                @if($order->status === 'completed' && $order->cash_received)
                <div class="paid-info">
                    <i class="bi bi-check-circle-fill"></i>
                    Bayar: Rp {{ number_format($order->cash_received, 0, ',', '.') }}<br>
                    Kembalian: Rp {{ number_format($order->change_amount, 0, ',', '.') }}
                </div>
                @endif

                <div class="order-controls">
                    @if($isBawaPulang)
                        @if(!in_array($order->status, ['completed', 'cancelled']))
                        <a href="{{ route('kasir.payment.show', $order->id) }}" class="btn-bayar">
                            <i class="bi bi-cash-coin"></i> Bayar
                        </a>
                        @else
                        <span class="label-sudah-bayar">
                            <i class="bi bi-check-circle"></i> Selesai
                        </span>
                        @endif
                    @else
                        @if(!in_array($order->status, ['completed', 'cancelled']))
                        <a href="{{ route('kasir.payment.show', $order->id) }}" class="btn-bayar">
                            <i class="bi bi-cash-coin"></i> Bayar
                        </a>
                        @endif
                        <select class="order-status-select" id="rs-{{ $order->id }}">
                            <option value="pending"   {{ $order->status==='pending'   ?'selected':'' }}>Pending</option>
                            <option value="confirmed" {{ $order->status==='confirmed' ?'selected':'' }}>Diproses</option>
                            <option value="completed" {{ $order->status==='completed' ?'selected':'' }}>Selesai</option>
                            <option value="cancelled" {{ $order->status==='cancelled' ?'selected':'' }}>Dibatalkan</option>
                        </select>
                        <button class="btn-update" onclick="updateStatus({{ $order->id }})">Update</button>
                    @endif

                    <button class="btn-hapus" onclick="openDelModal({{ $order->id }}, '{{ addslashes($order->customer_name) }}', '#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}')">
                        <i class="bi bi-trash3"></i>
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="orders-empty">
            <i class="bi bi-inbox"></i>
            Tidak ada order ditemukan.
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($orders->hasPages())
    <div class="orders-pagination">
        <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">

            @if($orders->onFirstPage())
            <span style="padding:6px 12px;border-radius:8px;border:1px solid #E8E0D5;color:#ccc;font-size:13px;cursor:not-allowed;">&#8249;</span>
            @else
            <a href="{{ $orders->previousPageUrl() }}&{{ http_build_query(request()->except('page')) }}"
               style="padding:6px 12px;border-radius:8px;border:1px solid #E8E0D5;color:#555;font-size:13px;text-decoration:none;">&#8249;</a>
            @endif

            @foreach($orders->getUrlRange(1, $orders->lastPage()) as $page => $url)
            @if($page == $orders->currentPage())
            <span style="padding:6px 12px;border-radius:8px;background:#2D5016;color:#fff;font-size:13px;font-weight:700;">{{ $page }}</span>
            @else
            <a href="{{ $url }}&{{ http_build_query(request()->except('page')) }}"
               style="padding:6px 12px;border-radius:8px;border:1px solid #E8E0D5;color:#555;font-size:13px;text-decoration:none;">{{ $page }}</a>
            @endif
            @endforeach

            @if($orders->hasMorePages())
            <a href="{{ $orders->nextPageUrl() }}&{{ http_build_query(request()->except('page')) }}"
               style="padding:6px 12px;border-radius:8px;border:1px solid #E8E0D5;color:#555;font-size:13px;text-decoration:none;">&#8250;</a>
            @else
            <span style="padding:6px 12px;border-radius:8px;border:1px solid #E8E0D5;color:#ccc;font-size:13px;cursor:not-allowed;">&#8250;</span>
            @endif

        </div>
        <div style="font-size:12px;color:#999;margin-top:8px;">
            Menampilkan {{ $orders->firstItem() }}&#8211;{{ $orders->lastItem() }} dari {{ $orders->total() }} order
        </div>
    </div>
    @endif

</div>

{{-- ── Modal Konfirmasi Hapus ── --}}
<div class="del-modal-wrap" id="delModalWrap">
    <div class="del-modal">
        <div class="del-modal-icon"><i class="bi bi-trash3-fill"></i></div>
        <div class="del-modal-title">Hapus Order?</div>
        <div class="del-modal-desc" id="delModalDesc">Order ini akan dihapus permanen.</div>
        <div class="del-modal-actions">
            <button class="del-btn-cancel" onclick="closeDelModal()">Batal</button>
            <button class="del-btn-ok" id="delBtnOk">Ya, Hapus</button>
        </div>
    </div>
</div>

{{-- ── Toast ── --}}
<div class="toast-k" id="toastK"></div>

@endsection

@push('custom_script')
<script>
var CSRF     = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
var BASE_URL = '{{ url("kasir/orders") }}';
var _delId   = null;

// ── Toast ─────────────────────────────────────────────────────────────────────
function showToast(msg, type) {
    var t = document.getElementById('toastK');
    t.textContent = msg;
    t.className = 'toast-k ' + (type === 'error' ? 'te' : 'ts') + ' show';
    setTimeout(function () { t.classList.remove('show'); }, 3000);
}

// ── Modal ─────────────────────────────────────────────────────────────────────
function openDelModal(id, name, code) {
    _delId = id;
    document.getElementById('delModalDesc').textContent =
        'Order ' + code + ' atas nama "' + name + '" akan dihapus permanen dan tidak bisa dikembalikan.';
    var btn = document.getElementById('delBtnOk');
    btn.disabled = false;
    btn.textContent = 'Ya, Hapus';
    document.getElementById('delModalWrap').classList.add('on');
}

function closeDelModal() {
    document.getElementById('delModalWrap').classList.remove('on');
    _delId = null;
}

// Klik luar modal = tutup
document.getElementById('delModalWrap').addEventListener('click', function (e) {
    if (e.target === this) closeDelModal();
});

// ── Konfirmasi Hapus — pakai XMLHttpRequest agar tidak kena intercept fetch ───
document.getElementById('delBtnOk').addEventListener('click', function () {
    if (!_delId) return;

    var btn = this;
    btn.disabled = true;
    btn.textContent = 'Menghapus...';

    var xhr = new XMLHttpRequest();
    xhr.open('DELETE', BASE_URL + '/' + _delId, true);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.setRequestHeader('X-CSRF-TOKEN', CSRF);
    xhr.setRequestHeader('Accept', 'application/json');

    xhr.onload = function () {
        try {
            var data = JSON.parse(xhr.responseText);
            if (data.success) {
                var card = document.getElementById('order-card-' + _delId);
                if (card) {
                    card.style.transition = 'opacity .3s, transform .3s';
                    card.style.opacity    = '0';
                    card.style.transform  = 'translateX(20px)';
                    setTimeout(function () {
                        card.remove();
                        var list = document.getElementById('ordersList');
                        if (list && list.querySelectorAll('.order-card').length === 0) {
                            list.innerHTML = '<div class="orders-empty"><i class="bi bi-inbox"></i>Tidak ada order ditemukan.</div>';
                        }
                    }, 300);
                }
                closeDelModal();
                showToast('Order berhasil dihapus!', 'success');
            } else {
                closeDelModal();
                showToast(data.message || 'Gagal menghapus order.', 'error');
            }
        } catch (e) {
            closeDelModal();
            showToast('Terjadi kesalahan, coba lagi.', 'error');
        }
    };

    xhr.onerror = function () {
        closeDelModal();
        showToast('Koneksi gagal, coba lagi.', 'error');
    };

    xhr.send();
});

// ── Update Status — pakai XMLHttpRequest juga ─────────────────────────────────
function updateStatus(id) {
    var selectEl = document.getElementById('rs-' + id);
    if (!selectEl) return;

    var xhr = new XMLHttpRequest();
    xhr.open('POST', BASE_URL + '/' + id + '/status', true);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.setRequestHeader('X-CSRF-TOKEN', CSRF);
    xhr.setRequestHeader('Accept', 'application/json');

    xhr.onload = function () {
        try {
            var data = JSON.parse(xhr.responseText);
            if (data.success) {
                showToast('Status berhasil diupdate!', 'success');
                setTimeout(function () { location.reload(); }, 800);
            } else {
                showToast(data.message || 'Gagal update status.', 'error');
            }
        } catch (e) {
            showToast('Terjadi kesalahan, coba lagi.', 'error');
        }
    };

    xhr.onerror = function () {
        showToast('Koneksi gagal, coba lagi.', 'error');
    };

    xhr.send(JSON.stringify({ status: selectEl.value }));
}
</script>
@endpush